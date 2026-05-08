<?php

use Illuminate\Support\Facades\Route;
use App\Models\Tuteur;
use App\Models\Admin;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});
Route::get('/media/{path}', MediaController::class)
    ->where('path', '.*')
    ->name('media.public');

/*
|--------------------------------------------------------------------------
| Protected Routes (Must be Logged In)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'prevent-back-history'])->group(function () {

    // 1. Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 2. The "Smart" Redirector
    // If a user types just "/dashboard", we find their table and send them home
    Route::get('/dashboard', function () {
        $userId = auth()->id();
        
        if (Admin::where('id_utilisateur', $userId)->exists()) {
            return redirect()->route('admin.dashboard');
        } 
        
        if (Tuteur::where('id_utilisateur', $userId)->exists()) {
            return redirect()->route('tuteur.dashboard');
        }

        return redirect()->route('etudiant.dashboard');
    })->name('dashboard');

    /*
    |----------------------------------------------------------------------
    | Role-Based Zones
    |----------------------------------------------------------------------
    */

    // 🎓 Student Zone
    Route::middleware(['is_etudiant'])->group(function () {
        Route::get('/student/dashboard', fn () => view('spa'))
            ->name('etudiant.dashboard');

        Route::view('/student/{any?}', 'spa')
            ->where('any', '.*');

        Route::redirect('/etudiant/dashboard', '/student/dashboard');
    });

    // 👨‍🏫 Tutor Zone
    Route::middleware(['is_tuteur'])->group(function () {
        Route::get('/tutor/dashboard', fn () => view('spa'))
            ->name('tuteur.dashboard');

        Route::view('/tutor/{any?}', 'spa')
            ->where('any', '.*');

        Route::redirect('/tuteur/dashboard', '/tutor/dashboard');
    });

    // ⚙️ Admin Zone
    Route::middleware(['is_admin'])->group(function () {
        Route::get('/admin/dashboard', fn () => view('spa'))
            ->name('admin.dashboard');

        Route::view('/admin/{any?}', 'spa')
            ->where('any', '.*');
    });

});

require __DIR__.'/auth.php';
