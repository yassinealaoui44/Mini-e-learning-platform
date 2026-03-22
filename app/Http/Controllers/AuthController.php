<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Utilisateur;
use App\Models\Etudiant;
use App\Models\Tuteur;
use App\Models\Admin;

class AuthController extends Controller
{
    // --- LOGIN ---

    public function showLogin()
    {
        return Inertia::render('Auth/Login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // 🚀 This tells Laravel to use the logic at the bottom of this file
            return $this->authenticated($request, Auth::user());
        }

        return back()->withErrors([
            'email' => 'Les identifiants ne correspondent pas.',
        ])->onlyInput('email');
    }

    // --- REGISTER ---

    // ✅ ADDED THIS: This fixes the 500 error you saw earlier
    public function showRegister()
    {
        return Inertia::render('Auth/Register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:utilisateurs',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:etudiant,tuteur',
        ]);

        DB::transaction(function () use ($request) {
            $user = Utilisateur::create([
                'nom' => $request->nom,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            if ($request->role === 'tuteur') {
                Tuteur::create(['id_utilisateur' => $user->id_utilisateur]);
            } else {
                Etudiant::create(['id_utilisateur' => $user->id_utilisateur]);
            }

            Auth::login($user);
        });

        // Redirect to the correct dashboard based on the role chosen
        return redirect()->route($request->role . '.dashboard');
    }

    // --- LOGOUT & REDIRECTION ---

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return Redirect::to('/login');
    }

    /**
     * This handles all redirections after login OR registration
     */
    protected function authenticated(Request $request, $user)
    {
        if (\App\Models\Admin::where('id_utilisateur', $user->id_utilisateur)->exists()) {
            return Redirect::intended('/admin/dashboard');
        }

        if (\App\Models\Tuteur::where('id_utilisateur', $user->id_utilisateur)->exists()) {
            return Redirect::intended('/tuteur/dashboard');
        }

        if (\App\Models\Etudiant::where('id_utilisateur', $user->id_utilisateur)->exists()) {
            return Redirect::intended('/etudiant/dashboard');
        }

        return redirect('/');
    }
}