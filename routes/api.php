<?php

use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\LessonController;
use App\Http\Controllers\Api\PortalController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'prevent-back-history'])->group(function () {
    Route::get('/me', [PortalController::class, 'me']);
    Route::get('/profile', [PortalController::class, 'profile']);
    Route::patch('/profile', [PortalController::class, 'updateProfile']);

    Route::get('/courses', [CourseController::class, 'index']);
    Route::post('/courses', [CourseController::class, 'store']);
    Route::get('/courses/{cours}', [CourseController::class, 'show']);
    Route::put('/courses/{cours}', [CourseController::class, 'update']);
    Route::delete('/courses/{cours}', [CourseController::class, 'destroy']);
    Route::post('/courses/{cours}/lessons', [CourseController::class, 'storeLesson']);

    Route::get('/lessons', [LessonController::class, 'index']);
    Route::post('/lessons', [LessonController::class, 'store']);
    Route::get('/lessons/{lecon}', [LessonController::class, 'show']);
    Route::put('/lessons/{lecon}', [LessonController::class, 'update']);
    Route::delete('/lessons/{lecon}', [LessonController::class, 'destroy']);

    Route::middleware(['is_etudiant'])->group(function () {
        Route::get('/student/dashboard', [PortalController::class, 'studentDashboard']);
        Route::get('/student/progress', [PortalController::class, 'studentProgress']);
        Route::post('/student/courses/{cours}/enroll', [CourseController::class, 'enroll']);
    });

    Route::middleware(['is_tuteur'])->group(function () {
        Route::get('/tutor/dashboard', [PortalController::class, 'tutorDashboard']);
        Route::get('/tutor/students', [PortalController::class, 'tutorStudents']);
        Route::get('/tutor/analytics', [PortalController::class, 'tutorAnalytics']);
    });

    Route::middleware(['is_admin'])->group(function () {
        Route::get('/admin/dashboard', [PortalController::class, 'adminDashboard']);
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{utilisateur}', [UserController::class, 'update']);
        Route::delete('/users/{utilisateur}', [UserController::class, 'destroy']);
    });
});
