<?php

namespace App\Http\Controllers\Api\Concerns;

use App\Models\Admin;
use App\Models\Cours;
use App\Models\Etudiant;
use App\Models\Lecon;
use App\Models\Tuteur;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait BuildsPortalResponses
{
    protected function resolveRole(): string
    {
        $userId = auth()->id();

        if (Admin::where('id_utilisateur', $userId)->exists()) {
            return 'admin';
        }

        if (Tuteur::where('id_utilisateur', $userId)->exists()) {
            return 'tutor';
        }

        if (Etudiant::where('id_utilisateur', $userId)->exists()) {
            return 'student';
        }

        abort(403, 'No portal role is assigned to this account.');
    }

    protected function currentStudent(): Etudiant
    {
        return Etudiant::query()
            ->with('user:id_utilisateur,prenom,nom,email')
            ->where('id_utilisateur', auth()->id())
            ->firstOrFail();
    }

    protected function currentTutor(): Tuteur
    {
        return Tuteur::query()
            ->with('user:id_utilisateur,prenom,nom,email')
            ->where('id_utilisateur', auth()->id())
            ->firstOrFail();
    }

    protected function currentAdmin(): Admin
    {
        return Admin::query()
            ->with('profile:id_utilisateur,prenom,nom,email')
            ->where('id_utilisateur', auth()->id())
            ->firstOrFail();
    }

    protected function serializeCourse(Cours $course, ?int $studentId = null, bool $includeLessons = false): array
    {
        $course->loadMissing('tuteur.user:id_utilisateur,prenom,nom,email');

        if (! array_key_exists('lecons_count', $course->getAttributes()) || ! array_key_exists('inscriptions_count', $course->getAttributes())) {
            $course->loadCount(['lecons', 'inscriptions']);
        }

        if ($includeLessons && ! $course->relationLoaded('lecons')) {
            $course->load([
                'lecons' => fn ($query) => $query->latest(),
            ]);
        }

        $ownerName = trim(($course->tuteur?->user?->prenom ?? '').' '.($course->tuteur?->user?->nom ?? ''));
        $lessons = $includeLessons
            ? $course->lecons
                ->map(fn (Lecon $lesson) => $this->serializeLesson($lesson, false))
                ->values()
                ->all()
            : [];

        return [
            'id' => $course->id,
            'title' => $course->nom,
            'track' => $course->filiere,
            'summary' => null,
            'thumbnail_url' => $course->cover_image_url,
            'cover_image_url' => $course->cover_image_url,
            'cover_image' => $course->thumbnail,
            'tutor_name' => $ownerName !== '' ? $ownerName : null,
            'owner_id' => $course->id_tuteur,
            'owner' => [
                'id' => $course->id_tuteur,
                'name' => $ownerName !== '' ? $ownerName : null,
            ],
            'lessons_count' => (int) $course->lecons_count,
            'students_count' => (int) $course->inscriptions_count,
            'created_at' => $course->created_at?->toIso8601String(),
            'created_at_label' => $course->created_at?->translatedFormat('d M Y'),
            'is_enrolled' => $studentId
                ? $course->inscriptions()->where('id_etudiant', $studentId)->exists()
                : false,
            'is_owned' => (int) $course->id_tuteur === (int) auth()->id(),
            'lessons' => $lessons,
            'modules' => $includeLessons
                ? [
                    [
                        'id' => 'course-content',
                        'title' => null,
                        'lessons' => $lessons,
                    ],
                ]
                : [],
        ];
    }

    protected function serializeLesson(Lecon $lesson, bool $includeFileUrl = true): array
    {
        $lesson->loadMissing('cours.tuteur.user:id_utilisateur,prenom,nom,email');

        $ownerName = trim(($lesson->cours?->tuteur?->user?->prenom ?? '').' '.($lesson->cours?->tuteur?->user?->nom ?? ''));

        return [
            'id' => $lesson->id,
            'title' => $lesson->titre,
            'type' => $lesson->type,
            'created_at' => $lesson->created_at?->toIso8601String(),
            'created_at_label' => $lesson->created_at?->translatedFormat('d M Y'),
            'file_url' => $includeFileUrl && $lesson->file_path ? route('media.public', ['path' => $lesson->file_path]) : null,
            'file_name' => $lesson->file_path ? basename($lesson->file_path) : null,
            'course' => [
                'id' => $lesson->cours?->id,
                'title' => $lesson->cours?->nom,
                'track' => $lesson->cours?->filiere,
                'thumbnail_url' => $lesson->cours?->thumbnail
                    ? route('media.public', ['path' => $lesson->cours?->thumbnail])
                    : null,
                'owner_id' => $lesson->cours?->id_tuteur,
                'owner_name' => $ownerName !== '' ? $ownerName : null,
            ],
        ];
    }

    protected function serializeUser(Utilisateur $user): array
    {
        $user->loadMissing(['adminProfile', 'tuteurProfile', 'etudiantProfile']);

        $roles = collect([
            $user->adminProfile ? 'admin' : null,
            $user->tuteurProfile ? 'tutor' : null,
            $user->etudiantProfile ? 'student' : null,
        ])->filter()->values();

        return [
            'id' => $user->id_utilisateur,
            'first_name' => $user->prenom,
            'last_name' => $user->nom,
            'full_name' => trim($user->prenom.' '.$user->nom),
            'email' => $user->email,
            'roles' => $roles,
            'primary_role' => $roles->first() ?? 'student',
            'is_admin' => $roles->contains('admin'),
            'track' => $user->etudiantProfile?->filiere,
            'level' => $user->etudiantProfile?->niveau,
            'domain' => $user->tuteurProfile?->domaine,
            'joined_at' => $user->created_at?->toIso8601String(),
            'joined_at_label' => $user->created_at?->translatedFormat('d M Y'),
            'initials' => Str::of(trim($user->prenom.' '.$user->nom))
                ->explode(' ')
                ->take(2)
                ->map(fn ($part) => Str::upper(Str::substr($part, 0, 1)))
                ->implode(''),
        ];
    }

    protected function serializeProfile(Utilisateur $user, string $role): array
    {
        $profile = [
            'track' => null,
            'level' => null,
            'domain' => null,
        ];

        if ($role === 'student') {
            $student = $this->currentStudent();
            $profile['track'] = $student->filiere;
            $profile['level'] = $student->niveau;
        }

        if ($role === 'tutor') {
            $tutor = $this->currentTutor();
            $profile['domain'] = $tutor->domaine;
        }

        return [
            'role' => $role,
            'user' => [
                'id' => $user->id_utilisateur,
                'first_name' => $user->prenom,
                'last_name' => $user->nom,
                'full_name' => trim($user->prenom.' '.$user->nom),
                'email' => $user->email,
            ],
            'profile' => $profile,
        ];
    }

    protected function ensureCourseCanBeManaged(Cours $course): void
    {
        $role = $this->resolveRole();

        abort_unless(
            $role === 'admin' || ($role === 'tutor' && (int) $course->id_tuteur === (int) auth()->id()),
            403,
        );
    }

    protected function ensureLessonCanBeManaged(Lecon $lesson): void
    {
        $lesson->loadMissing('cours');

        $role = $this->resolveRole();

        abort_unless(
            $role === 'admin' || ($role === 'tutor' && (int) $lesson->cours?->id_tuteur === (int) auth()->id()),
            403,
        );
    }
}
