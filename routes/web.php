<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Tuteur\DashboardController as TuteurDashboard;
use App\Http\Controllers\Etudiant\DashboardController as EtudiantDashboard;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Tuteur;
use App\Models\Etudiant;
use App\Models\Admin;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Send everyone straight to registration to start
Route::redirect('/', '/register');

/*
|--------------------------------------------------------------------------
| Protected Routes (Must be Logged In)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

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
        Route::get('/etudiant/dashboard', [EtudiantDashboard::class, 'index'])
            ->name('etudiant.dashboard');

        Route::post('/etudiant/cours/{cours}/inscription', [EtudiantDashboard::class, 'enroll'])
            ->name('etudiant.cours.enroll');
    });

    // 👨‍🏫 Tutor Zone
    Route::middleware(['is_tuteur'])->group(function () {
        Route::get('/tuteur/dashboard', [TuteurDashboard::class, 'index'])
            ->name('tuteur.dashboard');

        Route::post('/tuteur/cours', [TuteurDashboard::class, 'storeCourse'])
            ->name('tuteur.cours.store');

        Route::post('/tuteur/cours/{cours}/lecons', [TuteurDashboard::class, 'storeLesson'])
            ->name('tuteur.lecons.store');
    });

    // ⚙️ Admin Zone
    Route::middleware(['is_admin'])->group(function () {
        Route::get('/admin/dashboard', [AdminDashboard::class, 'index'])
            ->name('admin.dashboard');
    });

});

require __DIR__.'/auth.php';
