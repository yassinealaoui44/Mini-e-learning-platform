<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Tuteur\DashboardController as TuteurDashboard;
use App\Http\Controllers\Etudiant\DashboardController as EtudiantDashboard;

// 1. Redirect home to login
Route::get('/', function () {
    return redirect()->route('login');
});

// 2. Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 3. Register Routes
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

// 4. Protected Dashboards (Must be logged in)
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboard::class, 'index'])->name('admin.dashboard');
    Route::get('/tuteur/dashboard', [TuteurDashboard::class, 'index'])->name('tuteur.dashboard');
    Route::get('/etudiant/dashboard', [EtudiantDashboard::class, 'index'])->name('etudiant.dashboard');
});