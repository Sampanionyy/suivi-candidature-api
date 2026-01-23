<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\Profile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        try {
            $profile = $request->user()
                ->profile()
                ->with(['skills', 'jobContractTypes', 'workModes'])
                ->first();


            return response()->json([
                'success' => true,
                'message' => 'Profil récupéré avec succès',
                'data'    => $profile
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération du profil',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function update(UpdateProfileRequest $request)
    {
        try {
            $user = $request->user();

            $profile = $user->profile()->firstOrCreate(['user_id' => $user->id]);

            $this->updatePreferences($profile, $request);

            $profile->fill($request->validated());
            $profile->save();

            return response()->json([
                'success' => true,
                'message' => 'Profil mis à jour avec succès',
                'data'    => $profile->load(['skills', 'jobContractTypes', 'workModes']),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du profil',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    private function updatePreferences(Profile $profile, UpdateProfileRequest $request)
    {
        if ($request->has('skills')) {
            $profile->skills()->sync($request->skills ?? []);
        }

        if ($request->has('job_contract_types')) {
            $profile->jobContractTypes()->sync($request->job_contract_types ?? []);
        }

        if ($request->has('work_modes')) {
            $profile->workModes()->sync($request->work_modes ?? []);
        }
    }

    public function updatePhoto(Request $request)
    {
        try {
            $request->validate([
                'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $profile = $request->user()->profile ?? new Profile(['user_id' => $request->user()->id]);
            
            if ($request->hasFile('photo')) {
                // Supprimer l'ancienne photo si elle existe
                if ($profile->photo_url) {
                    // Extraire le chemin relatif (ex: 'profile_photos/xxx.jpg')
                    $oldPath = str_replace('/storage/', '', $profile->photo_url);
                    
                    // Supprimer du même disque 'public'
                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }

                // Stocker la nouvelle photo
                $file = $request->file('photo');
                $fileName = $file->store('profile_photos', 'public');
                
                // Construire l'URL complète
                $profile->photo_url = Storage::url($fileName);
            }

            $profile->save();

            return response()->json([
                'success' => true,
                'message' => 'Photo de profil mise à jour avec succès',
                'data'    => [
                    'id' => $profile->id,
                    'photo_url' => $profile->photo_url,
                    'updated_at' => $profile->updated_at,
                ]
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors'  => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Upload error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la photo de profil',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function deletePhoto(Request $request)
    {
        try {
            $profile = $request->user()->profile;

            if ($profile && $profile->photo_url) {

                $filePath = str_replace('/storage/', '', $profile->photo_url);
                Storage::disk('public')->delete($filePath);

                $profile->photo_url = null;
                $profile->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Photo de profil supprimée avec succès',
                'data'    => $profile
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de la photo de profil',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
