<?php

namespace App\Http\Controllers\Admin; // 🚀 THIS IS THE IMPORTANT PART

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        return Inertia::render('Admin/Dashboard');
    }
}