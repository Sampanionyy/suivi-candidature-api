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

        
       // Upload CV
        if ($request->hasFile('cv_path')) {
            $path = $request->file('cv_path')->store('cvs', 'public');
            $data['cv_path'] = Storage::url($path);
        }

        // Upload lettre de motivation
        if ($request->hasFile('cover_letter_path')) {
            $path = $request->file('cover_letter_path')->store('cover_letters', 'public');
            $data['cover_letter_path'] = Storage::url($path);
        }


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

        $data = $request->validated();

        if ($request->hasFile('cv_path')) {
            if ($application->cv_path) {
                $oldPath = str_replace('/storage/', '', $application->cv_path);
                Storage::disk('public')->delete($oldPath);
            }
            
            $path = $request->file('cv_path')->store('cvs', 'public');
            $data['cv_path'] = Storage::url($path);
        } else {
            unset($data['cv_path']);
        }

        if ($request->hasFile('cover_letter_path')) {
            if ($application->cover_letter_path) {
                $oldPath = str_replace('/storage/', '', $application->cover_letter_path);
                Storage::disk('public')->delete($oldPath);
            }
            
            $path = $request->file('cover_letter_path')->store('cover_letters', 'public');
            $data['cover_letter_path'] = Storage::url($path);
        } else {
            unset($data['cover_letter_path']);
        }

        $application->update($data);

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

    public function interviews()
    {
        try {
            $interviews = Application::whereNotNull('interview_date')
                ->orderBy('interview_date', 'asc')
                ->get(['id', 'position', 'company', 'interview_date', 'status']);

            return response()->json([
                'success' => true,
                'data' => $interviews,
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération des entretiens : ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la récupération des entretiens.',
            ], 500);
        }
    }

}
