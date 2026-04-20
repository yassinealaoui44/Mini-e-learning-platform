<?php

namespace App\Http\Middleware;

use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\Admin;
use App\Models\Tuteur;
use App\Models\Etudiant;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $role = null;
        $roleProfile = null;

        if ($user instanceof Utilisateur) {
            $user->loadMissing([
                'adminProfile:id_utilisateur',
                'tuteurProfile:id_utilisateur,domaine',
                'etudiantProfile:id_utilisateur,filiere,niveau',
            ]);

            $role = $this->getUserRole($user);
            $roleProfile = match ($role) {
                'tuteur' => $user->tuteurProfile,
                'etudiant' => $user->etudiantProfile,
                default => null,
            };
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id_utilisateur' => $user->id_utilisateur,
                    'prenom' => $user->prenom,
                    'nom' => $user->nom,
                    'email' => $user->email,
                    'role' => $role,
                    'filiere' => $roleProfile?->filiere,
                    'niveau' => $roleProfile?->niveau,
                    'domaine' => $roleProfile?->domaine,
                ] : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'info' => fn () => $request->session()->get('info'),
            ],
        ];
    }
    /**
     * Helper logic to detect user role based on your Database Schema.
     * This checks the child tables linked by id_utilisateur.
     */
    protected function getUserRole(Utilisateur $user): ?string
    {
        if ($user->adminProfile instanceof Admin) {
            return 'admin';
        }

        if ($user->tuteurProfile instanceof Tuteur) {
            return 'tuteur';
        }

        if ($user->etudiantProfile instanceof Etudiant) {
            return 'etudiant';
        }

        return null;
    }
}
