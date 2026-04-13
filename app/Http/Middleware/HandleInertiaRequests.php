<?php

namespace App\Http\Middleware;

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
    public function version(Request $request): ?string
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
        $role = $user ? $this->getUserRole($user) : null;
        $roleProfile = null;

        if ($user && $role === 'tuteur') {
            $roleProfile = Tuteur::query()
                ->select('domaine')
                ->where('id_utilisateur', $user->id_utilisateur)
                ->first();
        }

        if ($user && $role === 'etudiant') {
            $roleProfile = Etudiant::query()
                ->select('filiere', 'niveau')
                ->where('id_utilisateur', $user->id_utilisateur)
                ->first();
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
    protected function getUserRole($user): string
    {
        // Check if the user exists in the admin table
        if (Admin::where('id_utilisateur', $user->id_utilisateur)->exists()) {
            return 'admin';
        }
        
        // Check if the user exists in the tuteur table
        if (Tuteur::where('id_utilisateur', $user->id_utilisateur)->exists()) {
            return 'tuteur';
        }

        // If not admin or tuteur, they are a student by default
        return 'etudiant';
    }
}
