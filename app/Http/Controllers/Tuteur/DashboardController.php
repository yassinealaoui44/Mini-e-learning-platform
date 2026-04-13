<?php

namespace App\Http\Controllers\Tuteur;

use App\Http\Controllers\Controller;
use App\Models\Cours;
use App\Models\Lecon;
use App\Models\Tuteur;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the tutor dashboard.
     */
    public function index(): Response
    {
        $tuteur = Tuteur::query()
            ->where('id_utilisateur', auth()->id())
            ->firstOrFail();

        $courses = Cours::query()
            ->where('id_tuteur', $tuteur->id_utilisateur)
            ->with([
                'lecons' => fn ($query) => $query->latest(),
            ])
            ->withCount(['lecons', 'inscriptions'])
            ->latest()
            ->get();

        $recentLessons = $courses->isEmpty()
            ? collect()
            : Lecon::query()
                ->whereIn('cours_id', $courses->pluck('id'))
                ->with('cours:id,nom')
                ->latest()
                ->take(6)
                ->get();

        return Inertia::render('tuteur/Dashboard', [
            'stats' => [
                'courses' => $courses->count(),
                'lessons' => (int) $courses->sum('lecons_count'),
                'students' => (int) $courses->sum('inscriptions_count'),
                'filieres' => $courses->pluck('filiere')->filter()->unique()->count(),
            ],
            'courses' => $courses
                ->map(fn (Cours $course) => $this->serializeCourse($course))
                ->values(),
            'recentLessons' => $recentLessons
                ->map(fn (Lecon $lesson) => $this->serializeLesson($lesson))
                ->values(),
        ]);
    }

    public function storeCourse(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'filiere' => ['required', 'string', 'max:255'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ]);

        $thumbnailPath = $request->file('thumbnail')
            ? $request->file('thumbnail')->store('thumbnails', 'public')
            : null;

        Cours::create([
            'nom' => $validated['nom'],
            'filiere' => $validated['filiere'],
            'thumbnail' => $thumbnailPath,
            'id_tuteur' => auth()->id(),
        ]);

        return redirect()
            ->route('tuteur.dashboard')
            ->with('success', 'Le cours a été créé avec succès.');
    }

    public function storeLesson(Request $request, Cours $cours): RedirectResponse
    {
        abort_unless($cours->id_tuteur === auth()->id(), 403);

        $validated = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:pdf,video'],
            'file' => [
                'required',
                'file',
                'max:20480',
                function (string $attribute, mixed $value, \Closure $fail) use ($request): void {
                    $extension = strtolower($value->getClientOriginalExtension());

                    if ($request->input('type') === 'pdf' && $extension !== 'pdf') {
                        $fail('Un cours PDF doit être accompagné d’un fichier PDF.');
                    }

                    if (
                        $request->input('type') === 'video'
                        && ! in_array($extension, ['mp4', 'mov', 'avi', 'webm'], true)
                    ) {
                        $fail('Une leçon vidéo doit être envoyée au format MP4, MOV, AVI ou WEBM.');
                    }
                },
            ],
        ]);

        $filePath = $request->file('file')->store('lecons', 'public');

        $cours->lecons()->create([
            'titre' => $validated['titre'],
            'type' => $validated['type'],
            'file_path' => $filePath,
        ]);

        return redirect()
            ->route('tuteur.dashboard')
            ->with('success', 'La leçon a été ajoutée au cours.');
    }

    private function serializeCourse(Cours $course): array
    {
        return [
            'id' => $course->id,
            'nom' => $course->nom,
            'filiere' => $course->filiere,
            'thumbnail_url' => $course->thumbnail
                ? Storage::disk('public')->url($course->thumbnail)
                : null,
            'lessons_count' => (int) ($course->lecons_count ?? $course->lecons->count()),
            'students_count' => (int) ($course->inscriptions_count ?? $course->inscriptions->count()),
            'created_at' => $course->created_at?->format('d/m/Y'),
            'latest_lessons' => $course->lecons
                ->take(3)
                ->map(fn (Lecon $lesson) => [
                    'id' => $lesson->id,
                    'titre' => $lesson->titre,
                    'type' => $lesson->type,
                    'created_at' => $lesson->created_at?->format('d/m/Y'),
                ])
                ->values(),
        ];
    }

    private function serializeLesson(Lecon $lesson): array
    {
        return [
            'id' => $lesson->id,
            'titre' => $lesson->titre,
            'type' => $lesson->type,
            'cours_nom' => $lesson->cours?->nom,
            'created_at' => $lesson->created_at?->format('d/m/Y'),
        ];
    }
}
