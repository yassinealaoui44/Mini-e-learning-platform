<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Utilisateur;
use App\Models\Etudiant;
use App\Models\Tuteur;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $validated = $request->validate([
            'prenom' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:utilisateurs,email',
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => 'required|in:etudiant,tuteur',
            'filiere' => ['required_if:role,etudiant', 'nullable', 'string', Rule::in(config('academy.tracks', []))],
            'niveau' => ['required_if:role,etudiant', 'nullable', 'string', Rule::in(config('academy.levels', []))],
            'domaine' => ['required_if:role,tuteur', 'nullable', 'string', 'max:255'],
        ]);

        // 2-3. Create parent and role profile atomically
        $user = DB::transaction(function () use ($validated): Utilisateur {
            $created = Utilisateur::create([
                'prenom' => $validated['prenom'],
                'nom' => $validated['nom'],
                'email' => $validated['email'],
                'password' => $validated['password'],
            ]);

            if ($validated['role'] === 'etudiant') {
                Etudiant::create([
                    'id_utilisateur' => $created->id_utilisateur,
                    'filiere' => $validated['filiere'],
                    'niveau' => $validated['niveau'],
                ]);
            } else {
                Tuteur::create([
                    'id_utilisateur' => $created->id_utilisateur,
                    'domaine' => $validated['domaine'],
                ]);
            }

            return $created;
        });

        // 4. Fire registration event and log the user in
        event(new Registered($user));
        Auth::login($user);

        // 5. Dynamic redirect based on the role
        // Ensure routes are named 'etudiant.dashboard' and 'tuteur.dashboard' in web.php
        $target = $validated['role'] === 'tuteur'
            ? route('tuteur.dashboard', absolute: false)
            : route('etudiant.dashboard', absolute: false);

        return Inertia::location($target);
    }
}
