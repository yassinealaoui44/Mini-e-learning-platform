<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\BuildsPortalResponses;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Etudiant;
use App\Models\Tuteur;
use App\Models\Utilisateur;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    use BuildsPortalResponses;

    public function index(): JsonResponse
    {
        abort_unless($this->resolveRole() === 'admin', 403);

        $users = Utilisateur::query()
            ->with(['adminProfile', 'tuteurProfile', 'etudiantProfile'])
            ->latest()
            ->get();

        return response()->json([
            'users' => $users
                ->map(fn (Utilisateur $user) => $this->serializeUser($user))
                ->values(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        abort_unless($this->resolveRole() === 'admin', 403);

        $validated = $request->validate($this->rules());

        $user = Utilisateur::create([
            'prenom' => $validated['first_name'],
            'nom' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $this->syncRoleProfile($user, $validated);

        return response()->json([
            'message' => __('portal.user_created'),
            'user' => $this->serializeUser($user->fresh(['adminProfile', 'tuteurProfile', 'etudiantProfile'])),
        ], 201);
    }

    public function update(Request $request, Utilisateur $utilisateur): JsonResponse
    {
        abort_unless($this->resolveRole() === 'admin', 403);

        if ((int) $utilisateur->id_utilisateur === (int) auth()->id()) {
            return response()->json([
                'message' => __('portal.self_admin_update'),
            ], 422);
        }

        $validated = $request->validate($this->rules($utilisateur));

        if (
            $utilisateur->adminProfile
            && $validated['role'] !== 'admin'
            && Admin::count() <= 1
        ) {
            return response()->json([
                'message' => __('portal.keep_admin'),
            ], 422);
        }

        $utilisateur->update([
            'prenom' => $validated['first_name'],
            'nom' => $validated['last_name'],
            'email' => $validated['email'],
            ...($validated['password'] ? ['password' => Hash::make($validated['password'])] : []),
        ]);

        $this->syncRoleProfile($utilisateur, $validated);

        return response()->json([
            'message' => __('portal.user_updated'),
            'user' => $this->serializeUser($utilisateur->fresh(['adminProfile', 'tuteurProfile', 'etudiantProfile'])),
        ]);
    }

    public function destroy(Utilisateur $utilisateur): JsonResponse
    {
        abort_unless($this->resolveRole() === 'admin', 403);

        if ((int) $utilisateur->id_utilisateur === (int) auth()->id()) {
            return response()->json([
                'message' => __('portal.self_admin_delete'),
            ], 422);
        }

        if ($utilisateur->adminProfile && Admin::count() <= 1) {
            return response()->json([
                'message' => __('portal.keep_admin'),
            ], 422);
        }

        $utilisateur->delete();

        return response()->json([
            'message' => __('portal.user_deleted'),
        ]);
    }

    private function rules(?Utilisateur $user = null): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('utilisateurs', 'email')->ignore($user?->id_utilisateur, 'id_utilisateur'),
            ],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8', 'max:255'],
            'role' => ['required', Rule::in(['student', 'tutor', 'admin'])],
            'track' => ['nullable', 'string', Rule::in(config('academy.tracks', []))],
            'level' => ['nullable', 'string', Rule::in(config('academy.levels', []))],
            'domain' => ['nullable', 'string', 'max:255'],
        ];
    }

    private function syncRoleProfile(Utilisateur $user, array $validated): void
    {
        $role = $validated['role'];

        if ($role === 'student') {
            Etudiant::updateOrCreate(
                ['id_utilisateur' => $user->id_utilisateur],
                [
                    'filiere' => $validated['track'] ?? null,
                    'niveau' => $validated['level'] ?? null,
                ],
            );

            Tuteur::where('id_utilisateur', $user->id_utilisateur)->delete();
            Admin::where('id_utilisateur', $user->id_utilisateur)->delete();
            return;
        }

        if ($role === 'tutor') {
            Tuteur::updateOrCreate(
                ['id_utilisateur' => $user->id_utilisateur],
                [
                    'domaine' => $validated['domain'] ?? null,
                ],
            );

            Etudiant::where('id_utilisateur', $user->id_utilisateur)->delete();
            Admin::where('id_utilisateur', $user->id_utilisateur)->delete();
            return;
        }

        Admin::updateOrCreate([
            'id_utilisateur' => $user->id_utilisateur,
        ]);

        Etudiant::where('id_utilisateur', $user->id_utilisateur)->delete();
        Tuteur::where('id_utilisateur', $user->id_utilisateur)->delete();
    }
}
