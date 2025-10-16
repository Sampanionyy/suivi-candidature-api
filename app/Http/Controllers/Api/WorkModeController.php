<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWorkModeRequest;
use App\Http\Requests\UpdateWorkModeRequest;
use App\Http\Resources\WorkModeResource;
use App\Models\WorkMode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;

class WorkModeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection|JsonResponse
    {
        try {
            $workModes = WorkMode::orderBy('name')->get();
            
            return WorkModeResource::collection($workModes);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des modes de travail: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Une erreur est survenue lors de la récupération des modes de travail.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWorkModeRequest $request): WorkModeResource|JsonResponse
    {
        try {
            $workMode = WorkMode::create($request->validated());
            
            return (new WorkModeResource($workMode))
                ->response()
                ->setStatusCode(201);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du mode de travail: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Une erreur est survenue lors de la création du mode de travail.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): WorkModeResource|JsonResponse
    {
        try {
            $workMode = WorkMode::findOrFail($id);
            
            return new WorkModeResource($workMode);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Mode de travail non trouvé.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération du mode de travail: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Une erreur est survenue lors de la récupération du mode de travail.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWorkModeRequest $request, string $id): WorkModeResource|JsonResponse
    {
        try {
            $workMode = WorkMode::findOrFail($id);
            $workMode->update($request->validated());
            
            return new WorkModeResource($workMode);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Mode de travail non trouvé.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du mode de travail: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Une erreur est survenue lors de la mise à jour du mode de travail.',
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
            $workMode = WorkMode::findOrFail($id);
            $workMode->delete();
            
            return response()->json([
                'message' => 'Mode de travail supprimé avec succès.'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Mode de travail non trouvé.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression du mode de travail: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Une erreur est survenue lors de la suppression du mode de travail.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}