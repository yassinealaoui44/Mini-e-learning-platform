<?php

namespace App\Http\Controllers\Etudiant;

use App\Http\Controllers\Controller;
use App\Models\Cours;
use App\Models\Etudiant;
use App\Models\Inscription;
use App\Models\Lecon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the student dashboard.
     */
    public function index(): Response
    {
        $etudiant = Etudiant::query()
            ->where('id_utilisateur', auth()->id())
            ->firstOrFail();

        $studentId = $etudiant->id_utilisateur;

        $enrolledCourses = Cours::query()
            ->whereHas('inscriptions', fn ($query) => $query->where('id_etudiant', $studentId))
            ->with([
                'tuteur.user:id_utilisateur,prenom,nom',
                'lecons' => fn ($query) => $query->latest(),
                'inscriptions' => fn ($query) => $query
                    ->where('id_etudiant', $studentId)
                    ->latest('date_inscription'),
            ])
            ->withCount(['lecons', 'inscriptions'])
            ->latest()
            ->get();

        $enrolledCourseIds = $enrolledCourses->pluck('id');

        $recommendedCoursesQuery = Cours::query()
            ->when($etudiant->filiere, fn ($query, $filiere) => $query->where('filiere', $filiere))
            ->when(
                $enrolledCourseIds->isNotEmpty(),
                fn ($query) => $query->whereNotIn('id', $enrolledCourseIds),
            )
            ->with('tuteur.user:id_utilisateur,prenom,nom')
            ->withCount(['lecons', 'inscriptions'])
            ->latest();

        $recommendedCourses = $recommendedCoursesQuery
            ->take(6)
            ->get();

        $otherCourses = $etudiant->filiere
            ? Cours::query()
                ->where('filiere', '!=', $etudiant->filiere)
                ->when(
                    $enrolledCourseIds->isNotEmpty(),
                    fn ($query) => $query->whereNotIn('id', $enrolledCourseIds),
                )
                ->with('tuteur.user:id_utilisateur,prenom,nom')
                ->withCount(['lecons', 'inscriptions'])
                ->latest()
                ->take(4)
                ->get()
            : collect();

        $recentLessons = $enrolledCourseIds->isEmpty()
            ? collect()
            : Lecon::query()
                ->whereIn('cours_id', $enrolledCourseIds)
                ->with('cours:id,nom')
                ->latest()
                ->take(6)
                ->get();

        $matchingCoursesCount = Cours::query()
            ->when($etudiant->filiere, fn ($query, $filiere) => $query->where('filiere', $filiere))
            ->count();

        $activeTutorsCount = Cours::query()
            ->when($etudiant->filiere, fn ($query, $filiere) => $query->where('filiere', $filiere))
            ->distinct('id_tuteur')
            ->count('id_tuteur');

        return Inertia::render('Etudiant/Dashboard', [
            'stats' => [
                'enrolled_courses' => $enrolledCourses->count(),
                'matching_courses' => $matchingCoursesCount,
                'total_lessons' => (int) $enrolledCourses->sum('lecons_count'),
                'active_tutors' => $activeTutorsCount,
            ],
            'enrolledCourses' => $enrolledCourses
                ->map(fn (Cours $course) => $this->serializeCourse($course, true))
                ->values(),
            'recommendedCourses' => $recommendedCourses
                ->map(fn (Cours $course) => $this->serializeCourse($course))
                ->values(),
            'otherCourses' => $otherCourses
                ->map(fn (Cours $course) => $this->serializeCourse($course))
                ->values(),
            'recentLessons' => $recentLessons
                ->map(fn (Lecon $lesson) => $this->serializeLesson($lesson))
                ->values(),
        ]);
    }

    public function enroll(Cours $cours): RedirectResponse
    {
        $inscription = Inscription::firstOrCreate(
            [
                'id_etudiant' => auth()->id(),
                'id_cours' => $cours->id,
            ],
            [
                'date_inscription' => now(),
            ],
        );

        return redirect()
            ->route('etudiant.dashboard')
            ->with(
                $inscription->wasRecentlyCreated ? 'success' : 'info',
                $inscription->wasRecentlyCreated
                    ? 'Inscription confirmée. Le cours a été ajouté à votre tableau de bord.'
                    : 'Vous êtes déjà inscrit à ce cours.',
            );
    }

    private function serializeCourse(Cours $course, bool $includeEnrollment = false): array
    {
        $course->loadMissing([
            'tuteur.user:id_utilisateur,prenom,nom',
            'lecons' => fn ($query) => $query->latest(),
        ]);

        $enrollment = $includeEnrollment
            ? $course->inscriptions->first()
            : null;

        return [
            'id' => $course->id,
            'nom' => $course->nom,
            'filiere' => $course->filiere,
            'thumbnail_url' => $course->thumbnail
                ? Storage::disk('public')->url($course->thumbnail)
                : null,
            'tuteur_nom' => trim(($course->tuteur?->user?->prenom ?? '').' '.($course->tuteur?->user?->nom ?? '')),
            'lessons_count' => (int) ($course->lecons_count ?? $course->lecons->count()),
            'students_count' => (int) ($course->inscriptions_count ?? $course->inscriptions->count()),
            'created_at' => $course->created_at?->format('d/m/Y'),
            'enrolled_at' => $enrollment?->date_inscription
                ? date('d/m/Y', strtotime((string) $enrollment->date_inscription))
                : null,
            'latest_lessons' => $course->lecons
                ->take(3)
                ->map(fn (Lecon $lesson) => [
                    'id' => $lesson->id,
                    'titre' => $lesson->titre,
                    'type' => $lesson->type,
                    'created_at' => $lesson->created_at?->format('d/m/Y'),
                ])
                ->values(),
        ];
    }

    private function serializeLesson(Lecon $lesson): array
    {
        return [
            'id' => $lesson->id,
            'titre' => $lesson->titre,
            'type' => $lesson->type,
            'cours_nom' => $lesson->cours?->nom,
            'created_at' => $lesson->created_at?->format('d/m/Y'),
            'file_url' => $lesson->file_path
                ? Storage::disk('public')->url($lesson->file_path)
                : null,
        ];
    }
}
