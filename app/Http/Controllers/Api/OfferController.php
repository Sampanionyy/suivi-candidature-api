<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOfferRequest;
use App\Http\Requests\UpdateOfferRequest;
use App\Models\Offer;
use Illuminate\Http\JsonResponse;

class OfferController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $offers = Offer::with(['contractType', 'workMode'])->get();
            return response()->json(['success' => true, 'data' => $offers], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors de la récupération des offres'], 500);
        }
    }

    public function store(StoreOfferRequest $request): JsonResponse
    {
        try {
            $offer = Offer::create($request->validated());
            return response()->json(['success' => true, 'data' => $offer], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors de la création de l\'offre'], 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $offer = Offer::with(['contractType', 'workMode'])->findOrFail($id);
            return response()->json(['success' => true, 'data' => $offer], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Offre non trouvée'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors de la récupération de l\'offre'], 500);
        }
    }

    public function update(UpdateOfferRequest $request, string $id): JsonResponse
    {
        try {
            $offer = Offer::findOrFail($id);
            $offer->update($request->validated());
            return response()->json(['success' => true, 'data' => $offer], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Offre non trouvée'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors de la mise à jour de l\'offre'], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $offer = Offer::findOrFail($id);
            $offer->delete();
            return response()->json(['success' => true, 'message' => 'Offre supprimée'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Offre non trouvée'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors de la suppression de l\'offre'], 500);
        }
    }
}
