<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\BuildsPortalResponses;
use App\Http\Controllers\Controller;
use App\Models\Cours;
use App\Models\Lecon;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LessonController extends Controller
{
    use BuildsPortalResponses;

    public function index(Request $request): JsonResponse
    {
        abort_unless(in_array($this->resolveRole(), ['tutor', 'admin'], true), 403);

        $lessons = Lecon::query()
            ->with('cours.tuteur.user:id_utilisateur,prenom,nom,email')
            ->when(
                $this->resolveRole() === 'tutor',
                fn ($query) => $query->whereHas('cours', fn ($courseQuery) => $courseQuery->where('id_tuteur', auth()->id())),
            )
            ->when(
                $request->filled('course_id'),
                fn ($query) => $query->where('cours_id', (int) $request->integer('course_id')),
            )
            ->latest()
            ->get();

        return response()->json([
            'lessons' => $lessons
                ->map(fn (Lecon $lesson) => $this->serializeLesson($lesson))
                ->values(),
        ]);
    }

    public function show(Lecon $lecon): JsonResponse
    {
        $lecon->load('cours');
        $role = $this->resolveRole();

        if ($role === 'tutor') {
            $this->ensureLessonCanBeManaged($lecon);
        }

        if ($role === 'student') {
            abort_unless(
                $lecon->cours?->inscriptions()->where('id_etudiant', auth()->id())->exists(),
                403,
            );
        }

        return response()->json([
            'lesson' => $this->serializeLesson($lecon),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        abort_unless(in_array($this->resolveRole(), ['tutor', 'admin'], true), 403);

        $validated = $request->validate($this->lessonRules($request, true));

        $course = Cours::query()->findOrFail((int) $validated['cours_id']);
        $this->ensureCourseCanBeManaged($course);

        $lesson = $course->lecons()->create([
            'titre' => $validated['titre'],
            'type' => $validated['type'],
            'file_path' => $request->file('file')->store('lecons', 'public'),
        ]);

        return response()->json([
            'message' => __('portal.lesson_created'),
            'lesson' => $this->serializeLesson($lesson),
        ], 201);
    }

    public function update(Request $request, Lecon $lecon): JsonResponse
    {
        $this->ensureLessonCanBeManaged($lecon);

        $validated = $request->validate($this->lessonRules($request, false));

        if (! $request->file('file') && $validated['type'] !== $lecon->type) {
            return response()->json([
                'message' => __('portal.lesson_type_requires_file'),
            ], 422);
        }

        $lecon->titre = $validated['titre'];
        $lecon->type = $validated['type'];

        if ($request->file('file')) {
            if ($lecon->file_path) {
                Storage::disk('public')->delete($lecon->file_path);
            }

            $lecon->file_path = $request->file('file')->store('lecons', 'public');
        }

        $lecon->save();

        return response()->json([
            'message' => __('portal.lesson_updated'),
            'lesson' => $this->serializeLesson($lecon->fresh()),
        ]);
    }

    public function destroy(Lecon $lecon): JsonResponse
    {
        $this->ensureLessonCanBeManaged($lecon);

        if ($lecon->file_path) {
            Storage::disk('public')->delete($lecon->file_path);
        }

        $lecon->delete();

        return response()->json([
            'message' => __('portal.lesson_deleted'),
        ]);
    }

    private function lessonRules(Request $request, bool $fileRequired): array
    {
        return [
            'cours_id' => ['nullable', 'integer', 'exists:cours,id'],
            'titre' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:pdf,video'],
            'file' => [
                $fileRequired ? 'required' : 'nullable',
                'file',
                'max:20480',
                function (string $attribute, mixed $value, Closure $fail) use ($request): void {
                    if (! $value) {
                        return;
                    }

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
}
