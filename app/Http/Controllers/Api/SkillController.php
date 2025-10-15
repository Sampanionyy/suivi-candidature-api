<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSkillRequest;
use App\Http\Requests\UpdateSkillRequest;
use App\Http\Resources\SkillResource;
use App\Models\Skill;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;

class SkillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection|JsonResponse
    {
        try {
            $skills = Skill::orderBy('name')->get();
            
            return SkillResource::collection($skills);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des compétences: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Une erreur est survenue lors de la récupération des compétences.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSkillRequest $request): SkillResource|JsonResponse
    {
        try {
            $skill = Skill::create($request->validated());
            
            return (new SkillResource($skill))
                ->response()
                ->setStatusCode(201);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la compétence: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Une erreur est survenue lors de la création de la compétence.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): SkillResource|JsonResponse
    {
        try {
            $skill = Skill::findOrFail($id);
            
            return new SkillResource($skill);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Compétence non trouvée.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération de la compétence: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Une erreur est survenue lors de la récupération de la compétence.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSkillRequest $request, string $id): SkillResource|JsonResponse
    {
        try {
            $skill = Skill::findOrFail($id);
            $skill->update($request->validated());
            
            return new SkillResource($skill);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Compétence non trouvée.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de la compétence: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Une erreur est survenue lors de la mise à jour de la compétence.',
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
            $skill = Skill::findOrFail($id);
            $skill->delete();
            
            return response()->json([
                'message' => 'Compétence supprimée avec succès.'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Compétence non trouvée.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de la compétence: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Une erreur est survenue lors de la suppression de la compétence.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}