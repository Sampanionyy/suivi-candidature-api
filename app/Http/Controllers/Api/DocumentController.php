<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreDocumentRequest;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        try {
            $documents = $request->user()->documents;

            return response()->json([
                'success' => true,
                'message' => 'Documents récupérés avec succès',
                'data'    => $documents
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des documents',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function store(StoreDocumentRequest $request)
    {
        try {
            $file = $request->file('file');
            $path = $file->store('documents', 'public');

            $document = new Document([
                'user_id'  => $request->user()->id,
                'name'     => $request->name,
                'type'     => $request->type,
                'file_url' => Storage::url($path),
            ]);
            $document->save();

            return response()->json([
                'success' => true,
                'message' => 'Document ajouté avec succès',
                'data'    => $document
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l’ajout du document',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $document = $request->user()->documents()->findOrFail($id);

            // Supprime le fichier physique
            Storage::disk('public')->delete(str_replace('/storage/', '', $document->file_url));
            $document->delete();

            return response()->json([
                'success' => true,
                'message' => 'Document supprimé avec succès',
                'data'    => null
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du document',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
