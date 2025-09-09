<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\ApplicationRequest;
use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;


class ApplicationController extends Controller
{
    /**
     * Lister toutes les candidatures de l'utilisateur connecté
     */
    public function index(Request $request): JsonResponse
    {
        $query = Application::where('user_id', auth()->id())
            ->orderBy('applied_date', 'desc');

        // Filtres optionnels
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('company')) {
            $query->byCompany($request->company);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('position', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%");
            });
        }

        $applications = $query->get();

        return response()->json([
            'success' => true,
            'data' => ApplicationResource::collection($applications)
        ]);
    }

    /**
     * Créer une nouvelle candidature
     */
    public function store(ApplicationRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data['user_id'] = auth()->id(); 

        $application = Application::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Candidature créée avec succès',
            'data' => new ApplicationResource($application)
        ], 201);
    }


    /**
     * Afficher une candidature spécifique
     */
    public function show(Application $application): JsonResponse
    {
        if ($application->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => new ApplicationResource($application)
        ]);
    }

    /**
     * Mettre à jour une candidature
     */
    public function update(ApplicationRequest $request, Application $application): JsonResponse
    {
        if ($application->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé'
            ], 403);
        }

        $application->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Candidature mise à jour avec succès',
            'data' => new ApplicationResource($application)
        ]);
    }

    /**
     * Supprimer une candidature
     */
    public function destroy(Application $application): JsonResponse
    {
        if ($application->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé'
            ], 403);
        }

        if ($application->cv_path) {
            Storage::disk('public')->delete($application->cv_path);
        }
        
        if ($application->cover_letter_path) {
            Storage::disk('public')->delete($application->cover_letter_path);
        }

        $application->delete();

        return response()->json([
            'success' => true,
            'message' => 'Candidature supprimée avec succès'
        ]);
    }

    /**
     * Upload CV pour une candidature
     */
    public function uploadCV(Request $request, Application $application): JsonResponse
    {
        if ($application->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé'
            ], 403);
        }

        $request->validate([
            'cv' => 'required|file|mimes:pdf,doc,docx|max:5120' // Max 5MB
        ]);

        if ($application->cv_path) {
            Storage::disk('public')->delete($application->cv_path);
        }

        $path = $request->file('cv')->store('cvs', 'public');
        
        $application->update(['cv_path' => $path]);

        return response()->json([
            'success' => true,
            'message' => 'CV uploadé avec succès',
            'data' => [
                'cv_path' => $path,
                'cv_url' => asset('storage/' . $path)
            ]
        ]);
    }

    /**
     * Upload lettre de motivation pour une candidature
     */
    public function uploadCoverLetter(Request $request, Application $application): JsonResponse
    {
        if ($application->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé'
            ], 403);
        }

        $request->validate([
            'cover_letter' => 'required|file|mimes:pdf,doc,docx|max:5120' // Max 5MB
        ]);

        if ($application->cover_letter_path) {
            Storage::disk('public')->delete($application->cover_letter_path);
        }

        $path = $request->file('cover_letter')->store('cover-letters', 'public');
        
        $application->update(['cover_letter_path' => $path]);

        return response()->json([
            'success' => true,
            'message' => 'Lettre de motivation uploadée avec succès',
            'data' => [
                'cover_letter_path' => $path,
                'cover_letter_url' => asset('storage/' . $path)
            ]
        ]);
    }

    /**
     * Mettre à jour le statut d'une candidature (pour le drag & drop)
     */
    public function updateStatus(Request $request, Application $application): JsonResponse
    {
        if ($application->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé'
            ], 403);
        }

        $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(Application::STATUSES))
        ]);

        $application->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Statut mis à jour avec succès',
            'data' => new ApplicationResource($application)
        ]);
    }
}
