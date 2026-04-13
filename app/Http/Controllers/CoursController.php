<?php

namespace App\Http\Controllers\Tuteur;

use App\Http\Controllers\Controller;
use App\Models\Cours;
use App\Models\Lecon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function index()
    {
        // Get courses belonging ONLY to the logged-in tutor
        $courses = Cours::where('id_tuteur', auth()->id())
                        ->with('lecons')
                        ->get();

        return Inertia::render('Tuteur/Dashboard', [
            'courses' => $courses
        ]);
    }

    public function storeCourse(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'filiere' => 'required|string',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $path = $request->file('thumbnail')->store('thumbnails', 'public');

        Cours::create([
            'nom' => $request->nom,
            'filiere' => $request->filiere,
            'thumbnail' => $path,
            'id_tuteur' => auth()->id(),
        ]);

        return redirect()->back();
    }

    public function storeLecon(Request $request, $courseId)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'type' => 'required|in:pdf,video',
            'file' => 'required|file|mimes:pdf,mp4,mov,avi|max:20000', // 20MB limit
        ]);

        $path = $request->file('file')->store('lecons', 'public');

        Lecon::create([
            'titre' => $request->titre,
            'type' => $request->type,
            'file_path' => $path,
            'cours_id' => $courseId,
        ]);

        return redirect()->back();
    }
    
    // You would add deleteCourse and deleteLecon methods here using Storage::delete()
}