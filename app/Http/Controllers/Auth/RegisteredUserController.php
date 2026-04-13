<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Utilisateur;
use App\Models\Etudiant;
use App\Models\Tuteur;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    public function store(Request $request): RedirectResponse
    {
        // 1. Validation Logic
        $request->validate([
            'prenom'   => 'required|string|max:255',
            'nom'      => 'required|string|max:255',
            'email'    => 'required|string|lowercase|email|max:255|unique:utilisateurs,email',
            'password' => ['required', 'confirmed'],
            'role'     => 'required|in:etudiant,tuteur',
            
            // Validation rules for specific roles
            'filiere'  => 'required_if:role,etudiant|string|max:255|nullable',
            'niveau'   => 'required_if:role,etudiant|string|max:255|nullable',
            'domaine'  => 'required_if:role,tuteur|string|max:255|nullable',
        ]);

        // 2. Create the Parent User (Utilisateur)
        // Note: Password is saved in plain text as per your requirement
        $user = Utilisateur::create([
            'prenom'   => $request->prenom,
            'nom'      => $request->nom,
            'email'    => $request->email,
            'password' => $request->password, 
        ]);

        // 3. Create the Child Record (Etudiant OR Tuteur)
        if ($request->role === 'etudiant') {
            Etudiant::create([
                'id_utilisateur' => $user->id_utilisateur,
                'filiere'        => $request->filiere,
                'niveau'         => $request->niveau,
            ]);
        } else {
            Tuteur::create([
                'id_utilisateur' => $user->id_utilisateur,
                'domaine'        => $request->domaine,
            ]);
        }

        // 4. Fire registration event and log the user in
        event(new Registered($user));
        Auth::login($user);

        // 5. Dynamic redirect based on the role
        // Ensure routes are named 'etudiant.dashboard' and 'tuteur.dashboard' in web.php
        return redirect()->route($request->role . '.dashboard');
    }
}