<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Cours;
use App\Models\Etudiant;
use App\Models\Inscription;
use App\Models\Lecon;
use App\Models\Tuteur;
use App\Models\Utilisateur;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $users = Utilisateur::query()
            ->with(['adminProfile', 'tuteurProfile', 'etudiantProfile'])
            ->latest()
            ->get();

        $courses = Cours::query()
            ->with('tuteur.user:id_utilisateur,prenom,nom')
            ->withCount(['lecons', 'inscriptions'])
            ->latest()
            ->get();

        $recentLessons = Lecon::query()
            ->with('cours:id,nom')
            ->latest()
            ->take(8)
            ->get();

        $recentEnrollments = Inscription::query()
            ->with([
                'cours:id,nom',
                'etudiant.user:id_utilisateur,prenom,nom',
            ])
            ->latest('date_inscription')
            ->take(8)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_users' => Utilisateur::count(),
                'admins' => Admin::count(),
                'tuteurs' => Tuteur::count(),
                'etudiants' => Etudiant::count(),
                'courses' => Cours::count(),
                'lessons' => Lecon::count(),
                'enrollments' => Inscription::count(),
            ],
            'roleBreakdown' => [
                ['label' => 'Admins', 'value' => Admin::count(), 'tone' => 'sky'],
                ['label' => 'Tuteurs', 'value' => Tuteur::count(), 'tone' => 'teal'],
                ['label' => 'Etudiants', 'value' => Etudiant::count(), 'tone' => 'amber'],
            ],
            'users' => $users
                ->map(fn (Utilisateur $user) => $this->serializeUser($user))
                ->values(),
            'courses' => $courses
                ->map(fn (Cours $course) => $this->serializeCourse($course))
                ->values(),
            'recentLessons' => $recentLessons
                ->map(fn (Lecon $lesson) => [
                    'id' => $lesson->id,
                    'titre' => $lesson->titre,
                    'type' => $lesson->type,
                    'cours_nom' => $lesson->cours?->nom,
                    'created_at' => $lesson->created_at?->format('d/m/Y H:i'),
                ])
                ->values(),
            'recentEnrollments' => $recentEnrollments
                ->map(fn (Inscription $inscription) => [
                    'id' => $inscription->id_inscription,
                    'etudiant_nom' => trim(($inscription->etudiant?->user?->prenom ?? '').' '.($inscription->etudiant?->user?->nom ?? '')),
                    'cours_nom' => $inscription->cours?->nom,
                    'date_inscription' => $inscription->date_inscription
                        ? date('d/m/Y H:i', strtotime((string) $inscription->date_inscription))
                        : null,
                ])
                ->values(),
        ]);
    }

    public function toggleSuperUser(Utilisateur $utilisateur): RedirectResponse
    {
        if ($utilisateur->id_utilisateur === auth()->id()) {
            return redirect()
                ->route('admin.dashboard')
                ->with('error', 'Vous ne pouvez pas modifier votre propre acces super utilisateur.');
        }

        $adminProfile = Admin::query()
            ->where('id_utilisateur', $utilisateur->id_utilisateur)
            ->first();

        if ($adminProfile) {
            if (Admin::count() <= 1) {
                return redirect()
                    ->route('admin.dashboard')
                    ->with('error', 'Au moins un administrateur doit rester actif.');
            }

            $adminProfile->delete();

            return redirect()
                ->route('admin.dashboard')
                ->with('success', 'Les droits administrateur ont ete retires.');
        }

        Admin::create([
            'id_utilisateur' => $utilisateur->id_utilisateur,
        ]);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Le compte a ete promu super utilisateur.');
    }

    public function destroyCourse(Cours $cours): RedirectResponse
    {
        $courseName = $cours->nom;

        $cours->delete();

        return redirect()
            ->route('admin.dashboard')
            ->with('success', "Le cours {$courseName} a ete supprime.");
    }

    private function serializeUser(Utilisateur $user): array
    {
        $roles = collect([
            $user->adminProfile ? 'admin' : null,
            $user->tuteurProfile ? 'tuteur' : null,
            $user->etudiantProfile ? 'etudiant' : null,
        ])->filter()->values();

        return [
            'id_utilisateur' => $user->id_utilisateur,
            'prenom' => $user->prenom,
            'nom' => $user->nom,
            'full_name' => trim($user->prenom.' '.$user->nom),
            'email' => $user->email,
            'roles' => $roles,
            'primary_role' => $roles->first() ?? 'utilisateur',
            'is_admin' => $roles->contains('admin'),
            'domaine' => $user->tuteurProfile?->domaine,
            'filiere' => $user->etudiantProfile?->filiere,
            'niveau' => $user->etudiantProfile?->niveau,
            'joined_at' => $user->created_at?->format('d/m/Y'),
        ];
    }

    private function serializeCourse(Cours $course): array
    {
        return [
            'id' => $course->id,
            'nom' => $course->nom,
            'filiere' => $course->filiere,
            'thumbnail_url' => $course->thumbnail
                ? Storage::disk('public')->url($course->thumbnail)
                : null,
            'tuteur_nom' => trim(($course->tuteur?->user?->prenom ?? '').' '.($course->tuteur?->user?->nom ?? '')),
            'lessons_count' => (int) $course->lecons_count,
            'students_count' => (int) $course->inscriptions_count,
            'created_at' => $course->created_at?->format('d/m/Y'),
        ];
    }
}
