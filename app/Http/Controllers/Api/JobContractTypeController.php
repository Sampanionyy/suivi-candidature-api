<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJobContractTypeRequest;
use App\Http\Requests\UpdateJobContractTypeRequest;
use App\Http\Resources\JobContractTypeResource;
use App\Models\JobContractType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;

class JobContractTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection|JsonResponse
    {
        try {
            $jobContractTypes = JobContractType::orderBy('name')->get();
            
            return JobContractTypeResource::collection($jobContractTypes);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des types de contrat: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Une erreur est survenue lors de la récupération des types de contrat.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJobContractTypeRequest $request): JobContractTypeResource|JsonResponse
    {
        try {
            $jobContractType = JobContractType::create($request->validated());
            
            return (new JobContractTypeResource($jobContractType))
                ->response()
                ->setStatusCode(201);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du type de contrat: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Une erreur est survenue lors de la création du type de contrat.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JobContractTypeResource|JsonResponse
    {
        try {
            $jobContractType = JobContractType::findOrFail($id);
            
            return new JobContractTypeResource($jobContractType);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Type de contrat non trouvé.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération du type de contrat: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Une erreur est survenue lors de la récupération du type de contrat.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJobContractTypeRequest $request, string $id): JobContractTypeResource|JsonResponse
    {
        try {
            $jobContractType = JobContractType::findOrFail($id);
            $jobContractType->update($request->validated());
            
            return new JobContractTypeResource($jobContractType);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Type de contrat non trouvé.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du type de contrat: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Une erreur est survenue lors de la mise à jour du type de contrat.',
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
            $jobContractType = JobContractType::findOrFail($id);
            $jobContractType->delete();
            
            return response()->json([
                'message' => 'Type de contrat supprimé avec succès.'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Type de contrat non trouvé.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression du type de contrat: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Une erreur est survenue lors de la suppression du type de contrat.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}