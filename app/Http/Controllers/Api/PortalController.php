<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\BuildsPortalResponses;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Cours;
use App\Models\Etudiant;
use App\Models\Inscription;
use App\Models\Lecon;
use App\Models\Tuteur;
use App\Models\Utilisateur;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PortalController extends Controller
{
    use BuildsPortalResponses;

    public function me(): JsonResponse
    {
        /** @var Utilisateur $user */
        $user = auth()->user();

        return response()->json($this->serializeProfile($user, $this->resolveRole()));
    }

    public function profile(): JsonResponse
    {
        /** @var Utilisateur $user */
        $user = auth()->user();

        return response()->json($this->serializeProfile($user, $this->resolveRole()));
    }

    public function updateProfile(Request $request): JsonResponse
    {
        /** @var Utilisateur $user */
        $user = auth()->user();
        $role = $this->resolveRole();

        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('utilisateurs', 'email')->ignore($user->id_utilisateur, 'id_utilisateur'),
            ],
        ];

        if ($role === 'student') {
            $rules['track'] = ['nullable', 'string', Rule::in(config('academy.tracks', []))];
            $rules['level'] = ['nullable', 'string', Rule::in(config('academy.levels', []))];
        }

        if ($role === 'tutor') {
            $rules['domain'] = ['nullable', 'string', 'max:255'];
        }

        $validated = $request->validate($rules);

        $user->update([
            'prenom' => $validated['first_name'],
            'nom' => $validated['last_name'],
            'email' => $validated['email'],
        ]);

        if ($role === 'student') {
            $this->currentStudent()->update([
                'filiere' => $validated['track'] ?? null,
                'niveau' => $validated['level'] ?? null,
            ]);
        }

        if ($role === 'tutor') {
            $this->currentTutor()->update([
                'domaine' => $validated['domain'] ?? null,
            ]);
        }

        return response()->json([
            ...$this->serializeProfile($user->fresh(), $role),
            'message' => __('portal.profile_updated'),
        ]);
    }

    public function studentDashboard(): JsonResponse
    {
        $student = $this->currentStudent();
        $studentId = $student->id_utilisateur;
        $enrolledCourseIds = $student->inscriptions()->pluck('id_cours');

        $enrolledCourses = $enrolledCourseIds->isEmpty()
            ? collect()
            : Cours::query()
                ->whereIn('id', $enrolledCourseIds)
                ->with('tuteur.user:id_utilisateur,prenom,nom,email')
                ->withCount(['lecons', 'inscriptions'])
                ->latest()
                ->take(4)
                ->get();

        $recommendedQuery = Cours::query()
            ->when($student->filiere, fn ($query, $track) => $query->where('filiere', $track))
            ->when($enrolledCourseIds->isNotEmpty(), fn ($query) => $query->whereNotIn('id', $enrolledCourseIds))
            ->with('tuteur.user:id_utilisateur,prenom,nom,email')
            ->withCount(['lecons', 'inscriptions'])
            ->latest();

        $recommendedCourses = (clone $recommendedQuery)
            ->take(3)
            ->get();

        $recentLessons = $enrolledCourseIds->isEmpty()
            ? collect()
            : Lecon::query()
                ->whereIn('cours_id', $enrolledCourseIds)
                ->with('cours:id,nom,filiere,id_tuteur')
                ->latest()
                ->take(5)
                ->get();

        $totalLessons = $enrolledCourseIds->isEmpty()
            ? 0
            : (int) Cours::query()
                ->whereIn('id', $enrolledCourseIds)
                ->withCount('lecons')
                ->get()
                ->sum('lecons_count');

        $activeTutors = Cours::query()
            ->when($student->filiere, fn ($query, $track) => $query->where('filiere', $track))
            ->distinct('id_tuteur')
            ->count('id_tuteur');

        return response()->json([
            'stats' => [
                'enrolled_courses' => $enrolledCourseIds->count(),
                'recommended_courses' => $recommendedQuery->count(),
                'total_lessons' => $totalLessons,
                'active_tutors' => $activeTutors,
            ],
            'enrolled_courses' => $enrolledCourses
                ->map(fn (Cours $course) => $this->serializeCourse($course, $studentId))
                ->values(),
            'recommended_courses' => $recommendedCourses
                ->map(fn (Cours $course) => $this->serializeCourse($course, $studentId))
                ->values(),
            'recent_lessons' => $recentLessons
                ->map(fn (Lecon $lesson) => $this->serializeLesson($lesson))
                ->values(),
        ]);
    }

    public function studentProgress(): JsonResponse
    {
        $student = $this->currentStudent();
        $courses = $student->courses()
            ->with('tuteur.user:id_utilisateur,prenom,nom,email')
            ->withCount(['lecons', 'inscriptions'])
            ->latest()
            ->get();

        return response()->json([
            'stats' => [
                'enrolled_courses' => $courses->count(),
                'total_lessons' => (int) $courses->sum('lecons_count'),
                'tracking_label' => __('portal.status_partial'),
            ],
            'note' => __('portal.progress_note'),
            'courses' => $courses
                ->map(function (Cours $course) use ($student): array {
                    return [
                        ...$this->serializeCourse($course, $student->id_utilisateur),
                        'progress_note' => __('portal.progress_course_note'),
                    ];
                })
                ->values(),
        ]);
    }

    public function tutorDashboard(): JsonResponse
    {
        $courses = Cours::query()
            ->where('id_tuteur', auth()->id())
            ->with('tuteur.user:id_utilisateur,prenom,nom,email')
            ->withCount(['lecons', 'inscriptions'])
            ->latest()
            ->get();

        $recentLessons = $courses->isEmpty()
            ? collect()
            : Lecon::query()
                ->whereIn('cours_id', $courses->pluck('id'))
                ->with('cours:id,nom,filiere,id_tuteur')
                ->latest()
                ->take(5)
                ->get();

        return response()->json([
            'stats' => [
                'courses' => $courses->count(),
                'lessons' => (int) $courses->sum('lecons_count'),
                'students' => (int) $courses->sum('inscriptions_count'),
                'tracks' => $courses->pluck('filiere')->filter()->unique()->count(),
            ],
            'courses' => $courses
                ->take(4)
                ->map(fn (Cours $course) => $this->serializeCourse($course))
                ->values(),
            'recent_lessons' => $recentLessons
                ->map(fn (Lecon $lesson) => $this->serializeLesson($lesson))
                ->values(),
        ]);
    }

    public function tutorStudents(): JsonResponse
    {
        $students = Etudiant::query()
            ->whereHas('inscriptions.cours', fn ($query) => $query->where('id_tuteur', auth()->id()))
            ->with([
                'user:id_utilisateur,prenom,nom,email',
                'inscriptions' => fn ($query) => $query
                    ->whereHas('cours', fn ($courseQuery) => $courseQuery->where('id_tuteur', auth()->id()))
                    ->with('cours:id,nom,filiere')
                    ->latest('date_inscription'),
            ])
            ->get()
            ->map(function (Etudiant $student): array {
                return [
                    'id' => $student->id_utilisateur,
                    'full_name' => trim(($student->user?->prenom ?? '').' '.($student->user?->nom ?? '')),
                    'email' => $student->user?->email,
                    'enrolled_courses_count' => $student->inscriptions->count(),
                    'latest_enrollment_at' => $student->inscriptions->first()?->date_inscription,
                    'latest_enrollment' => $student->inscriptions->first()?->date_inscription
                        ? Carbon::parse((string) $student->inscriptions->first()?->date_inscription)->translatedFormat('d M Y')
                        : null,
                    'tracks' => $student->inscriptions
                        ->map(fn ($inscription) => $inscription->cours?->filiere)
                        ->filter()
                        ->unique()
                        ->values()
                        ->all(),
                ];
            })
            ->values();

        return response()->json([
            'summary' => [
                'students' => $students->count(),
                'active_courses' => Cours::query()
                    ->where('id_tuteur', auth()->id())
                    ->whereHas('inscriptions')
                    ->count(),
                'average_courses_per_student' => $students->count() > 0
                    ? round($students->avg('enrolled_courses_count'), 1)
                    : 0,
            ],
            'students' => $students,
        ]);
    }

    public function tutorAnalytics(): JsonResponse
    {
        $courses = Cours::query()
            ->where('id_tuteur', auth()->id())
            ->with('tuteur.user:id_utilisateur,prenom,nom,email')
            ->withCount(['lecons', 'inscriptions'])
            ->latest()
            ->get();
        $courseIds = $courses->pluck('id');
        $now = Carbon::now();

        $monthlyEnrollments = $courseIds->isEmpty()
            ? 0
            : Inscription::query()
                ->whereIn('id_cours', $courseIds)
                ->whereBetween('date_inscription', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])
                ->count();

        $yearlyEnrollments = $courseIds->isEmpty()
            ? 0
            : Inscription::query()
                ->whereIn('id_cours', $courseIds)
                ->whereBetween('date_inscription', [$now->copy()->startOfYear(), $now->copy()->endOfYear()])
                ->count();

        return response()->json([
            'summary' => [
                'courses' => $courses->count(),
                'lessons' => (int) $courses->sum('lecons_count'),
                'students' => (int) $courses->sum('inscriptions_count'),
                'average_lessons_per_course' => $courses->count() > 0
                    ? round($courses->avg('lecons_count'), 1)
                    : 0,
                'monthly_enrollments' => $monthlyEnrollments,
                'yearly_enrollments' => $yearlyEnrollments,
            ],
            'top_courses' => $courses
                ->sortByDesc('inscriptions_count')
                ->take(4)
                ->map(fn (Cours $course) => $this->serializeCourse($course))
                ->values(),
            'courses' => $courses
                ->map(fn (Cours $course) => $this->serializeCourse($course))
                ->values(),
        ]);
    }

    public function adminDashboard(): JsonResponse
    {
        abort_unless($this->resolveRole() === 'admin', 403);

        $users = Utilisateur::query()
            ->with(['adminProfile', 'tuteurProfile', 'etudiantProfile'])
            ->latest()
            ->take(6)
            ->get();

        $courses = Cours::query()
            ->with('tuteur.user:id_utilisateur,prenom,nom,email')
            ->withCount(['lecons', 'inscriptions'])
            ->latest()
            ->take(6)
            ->get();

        $recentLessons = Lecon::query()
            ->with('cours.tuteur.user:id_utilisateur,prenom,nom,email')
            ->latest()
            ->take(6)
            ->get();

        return response()->json([
            'stats' => [
                'users' => Utilisateur::count(),
                'admins' => Admin::count(),
                'tutors' => Tuteur::count(),
                'students' => Etudiant::count(),
                'courses' => Cours::count(),
                'lessons' => Lecon::count(),
                'enrollments' => Inscription::count(),
            ],
            'role_breakdown' => [
                ['label' => __('portal.role_admin'), 'value' => Admin::count()],
                ['label' => __('portal.role_tutor'), 'value' => Tuteur::count()],
                ['label' => __('portal.role_student'), 'value' => Etudiant::count()],
            ],
            'users' => $users
                ->map(fn (Utilisateur $user) => $this->serializeUser($user))
                ->values(),
            'courses' => $courses
                ->map(fn (Cours $course) => $this->serializeCourse($course))
                ->values(),
            'recent_lessons' => $recentLessons
                ->map(fn (Lecon $lesson) => $this->serializeLesson($lesson))
                ->values(),
        ]);
    }
}
