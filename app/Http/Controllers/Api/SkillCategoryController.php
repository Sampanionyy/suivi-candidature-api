<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSkillCategoryRequest;
use App\Http\Requests\UpdateSkillCategoryRequest;
use App\Http\Resources\SkillCategoryResource;
use App\Models\SkillCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;

class SkillCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection|JsonResponse
    {
        try {
            $categories = SkillCategory::query()
                ->withCount('skills')
                ->orderBy('name')
                ->get();
            
            return SkillCategoryResource::collection($categories);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des catégories: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Une erreur est survenue lors de la récupération des catégories.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSkillCategoryRequest $request): SkillCategoryResource|JsonResponse
    {
        try {
            $category = SkillCategory::create($request->validated());
            
            return (new SkillCategoryResource($category))
                ->response()
                ->setStatusCode(201);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la catégorie: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Une erreur est survenue lors de la création de la catégorie.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): SkillCategoryResource|JsonResponse
    {
        try {
            $category = SkillCategory::query()
                ->with('skills')
                ->withCount('skills')
                ->findOrFail($id);
            
            return new SkillCategoryResource($category);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Catégorie non trouvée.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération de la catégorie: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Une erreur est survenue lors de la récupération de la catégorie.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSkillCategoryRequest $request, string $id): SkillCategoryResource|JsonResponse
    {
        try {
            $category = SkillCategory::findOrFail($id);
            $category->update($request->validated());
            
            return new SkillCategoryResource($category);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Catégorie non trouvée.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de la catégorie: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Une erreur est survenue lors de la mise à jour de la catégorie.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $category = SkillCategory::withCount('skills')->findOrFail($id);
            
            // Vérifier si la catégorie a des compétences associées
            if ($category->skills_count > 0) {
                return response()->json([
                    'message' => 'Impossible de supprimer cette catégorie car elle contient des compétences.',
                    'skills_count' => $category->skills_count
                ], 422);
            }
            
            $category->delete();
            
            return response()->json([
                'message' => 'Catégorie supprimée avec succès.'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Catégorie non trouvée.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de la catégorie: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Une erreur est survenue lors de la suppression de la catégorie.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
