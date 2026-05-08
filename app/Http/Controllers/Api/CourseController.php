<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\BuildsPortalResponses;
use App\Http\Controllers\Controller;
use App\Models\Cours;
use App\Models\Inscription;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    use BuildsPortalResponses;

    public function index(Request $request): JsonResponse
    {
        $scope = $request->string('scope')->toString();
        $role = $this->resolveRole();

        $courses = match ($scope) {
            'all' => $this->allCourses(),
            'owned' => $this->ownedCourses(),
            'discover' => $this->discoverCourses(),
            'enrolled' => $this->enrolledCourses(),
            default => $role === 'admin'
                ? $this->allCourses()
                : ($role === 'tutor' ? $this->ownedCourses() : $this->enrolledCourses()),
        };

        $studentId = $role === 'student' ? auth()->id() : null;

        return response()->json([
            'courses' => $courses
                ->map(fn (Cours $course) => $this->serializeCourse($course, $studentId))
                ->values(),
        ]);
    }

    public function show(Cours $cours): JsonResponse
    {
        $role = $this->resolveRole();

        if ($role === 'tutor') {
            $this->ensureCourseCanBeManaged($cours);
        }

        $cours->load('tuteur.user:id_utilisateur,prenom,nom,email');
        $cours->loadCount(['lecons', 'inscriptions']);

        return response()->json([
            'course' => $this->serializeCourse($cours, $role === 'student' ? auth()->id() : null, true),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        abort_unless(in_array($this->resolveRole(), ['tutor', 'admin'], true), 403);

        $validated = $request->validate($this->courseRules());

        $coverImage = $request->file('cover_image') ?? $request->file('thumbnail');

        $course = Cours::create([
            'nom' => $validated['nom'],
            'filiere' => $validated['filiere'],
            'thumbnail' => $coverImage
                ? $coverImage->store('covers', 'public')
                : null,
            'id_tuteur' => $this->resolveRole() === 'admin'
                ? (int) $validated['id_tuteur']
                : (int) auth()->id(),
        ]);

        $course->load('tuteur.user:id_utilisateur,prenom,nom,email');
        $course->loadCount(['lecons', 'inscriptions']);

        return response()->json([
            'message' => __('portal.course_created'),
            'course' => $this->serializeCourse($course),
        ], 201);
    }

    public function update(Request $request, Cours $cours): JsonResponse
    {
        $this->ensureCourseCanBeManaged($cours);

        $validated = $request->validate($this->courseRules());

        $coverImage = $request->file('cover_image') ?? $request->file('thumbnail');

        if ($coverImage) {
            if ($cours->thumbnail) {
                Storage::disk('public')->delete($cours->thumbnail);
            }

            $cours->thumbnail = $coverImage->store('covers', 'public');
        }

        $cours->nom = $validated['nom'];
        $cours->filiere = $validated['filiere'];

        if ($this->resolveRole() === 'admin') {
            $cours->id_tuteur = (int) $validated['id_tuteur'];
        }

        $cours->save();

        $cours->load('tuteur.user:id_utilisateur,prenom,nom,email');
        $cours->loadCount(['lecons', 'inscriptions']);

        return response()->json([
            'message' => __('portal.course_updated'),
            'course' => $this->serializeCourse($cours),
        ]);
    }

    public function destroy(Cours $cours): JsonResponse
    {
        $this->ensureCourseCanBeManaged($cours);

        if ($cours->thumbnail) {
            Storage::disk('public')->delete($cours->thumbnail);
        }

        $cours->lecons()->get()->each(function ($lesson): void {
            if ($lesson->file_path) {
                Storage::disk('public')->delete($lesson->file_path);
            }
        });

        $cours->delete();

        return response()->json([
            'message' => __('portal.course_deleted'),
        ]);
    }

    public function enroll(Cours $cours): JsonResponse
    {
        abort_unless($this->resolveRole() === 'student', 403);

        $inscription = Inscription::firstOrCreate(
            [
                'id_etudiant' => auth()->id(),
                'id_cours' => $cours->id,
            ],
            [
                'date_inscription' => now(),
            ],
        );

        return response()->json([
            'created' => $inscription->wasRecentlyCreated,
            'message' => $inscription->wasRecentlyCreated
                ? __('portal.enrollment_confirmed')
                : __('portal.already_enrolled'),
        ]);
    }

    public function storeLesson(Request $request, Cours $cours): JsonResponse
    {
        $this->ensureCourseCanBeManaged($cours);

        $validated = $request->validate($this->lessonRules($request));

        $lesson = $cours->lecons()->create([
            'titre' => $validated['titre'],
            'type' => $validated['type'],
            'file_path' => $request->file('file')->store('lecons', 'public'),
        ]);

        return response()->json([
            'message' => __('portal.lesson_created'),
            'lesson' => $this->serializeLesson($lesson),
        ], 201);
    }

    private function courseRules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:255'],
            'filiere' => ['required', 'string', Rule::in(config('academy.tracks', []))],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'id_tuteur' => [
                Rule::requiredIf($this->resolveRole() === 'admin'),
                'nullable',
                'integer',
                'exists:tuteurs,id_utilisateur',
            ],
        ];
    }

    private function lessonRules(Request $request): array
    {
        return [
            'titre' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:pdf,video'],
            'file' => [
                'required',
                'file',
                'max:20480',
                function (string $attribute, mixed $value, Closure $fail) use ($request): void {
                    $extension = strtolower($value->getClientOriginalExtension());

                    if ($request->input('type') === 'pdf' && $extension !== 'pdf') {
                        $fail(__('validation.custom.lesson_pdf'));
                    }

                    if ($request->input('type') === 'video' && ! in_array($extension, ['mp4', 'mov', 'avi', 'webm'], true)) {
                        $fail(__('validation.custom.lesson_video'));
                    }
                },
            ],
        ];
    }

    private function enrolledCourses()
    {
        abort_unless($this->resolveRole() === 'student', 403);

        return Cours::query()
            ->whereHas('inscriptions', fn ($query) => $query->where('id_etudiant', auth()->id()))
            ->with('tuteur.user:id_utilisateur,prenom,nom,email')
            ->withCount(['lecons', 'inscriptions'])
            ->latest()
            ->get();
    }

    private function discoverCourses()
    {
        abort_unless($this->resolveRole() === 'student', 403);

        $student = $this->currentStudent();
        $enrolledCourseIds = $student->inscriptions()->pluck('id_cours');

        return Cours::query()
            ->when($student->filiere, fn ($query, $track) => $query->where('filiere', $track))
            ->when($enrolledCourseIds->isNotEmpty(), fn ($query) => $query->whereNotIn('id', $enrolledCourseIds))
            ->with('tuteur.user:id_utilisateur,prenom,nom,email')
            ->withCount(['lecons', 'inscriptions'])
            ->latest()
            ->get();
    }

    private function ownedCourses()
    {
        abort_unless(in_array($this->resolveRole(), ['tutor', 'admin'], true), 403);

        return Cours::query()
            ->where('id_tuteur', auth()->id())
            ->with('tuteur.user:id_utilisateur,prenom,nom,email')
            ->withCount(['lecons', 'inscriptions'])
            ->latest()
            ->get();
    }

    private function allCourses()
    {
        abort_unless($this->resolveRole() === 'admin', 403);

        return Cours::query()
            ->with('tuteur.user:id_utilisateur,prenom,nom,email')
            ->withCount(['lecons', 'inscriptions'])
            ->latest()
            ->get();
    }
}
