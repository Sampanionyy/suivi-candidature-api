<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FollowUpController extends Controller
{
    /**
     * Liste toutes les candidatures nécessitant une relance
     * 
     * GET /api/applications/follow-ups
     */
    public function index(Request $request): JsonResponse
    {
        $applications = Application::where('user_id', $request->user()->id)
            ->needingFollowUp()
            ->with('user:id,name,email')
            ->orderBy('last_follow_up_date')
            ->orderBy('applied_date')
            ->get()
            ->map(function ($app) {
                return [
                    'id' => $app->id,
                    'position' => $app->position,
                    'company' => $app->company,
                    'status' => $app->status,
                    'applied_date' => $app->applied_date?->format('Y-m-d'),
                    'last_follow_up_date' => $app->last_follow_up_date?->format('Y-m-d'),
                    'follow_up_count' => $app->follow_up_count,
                    'days_since_last_contact' => $app->daysSinceLastContact(),
                    'needs_follow_up' => $app->needs_follow_up,
                ];
            });
            
        return response()->json([
            'success' => true,
            'count' => $applications->count(),
            'data' => $applications,
        ]);
    }

    /**
     * Marque qu'une relance a été envoyée pour une candidature
     * 
     * POST /api/applications/{id}/mark-follow-up-sent
     */
    public function markFollowUpSent(Request $request, Application $application): JsonResponse
    {
        // Vérifie que l'application appartient bien à l'utilisateur
        if ($application->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Candidature non trouvée.',
            ], 404);
        }

        if (!$application->needs_follow_up) {
            return response()->json([
                'success' => false,
                'message' => 'Cette candidature ne nécessite pas de relance pour le moment.',
            ], 400);
        }

        // Marque la relance comme envoyée
        $application->markFollowUpSent();

        return response()->json([
            'success' => true,
            'message' => 'Relance marquée comme envoyée !',
            'data' => [
                'id' => $application->id,
                'last_follow_up_date' => $application->last_follow_up_date->format('Y-m-d'),
                'follow_up_count' => $application->follow_up_count,
                'needs_follow_up' => $application->needs_follow_up,
            ],
        ]);
    }

    /**
     * Réinitialise le statut de relance (si besoin d'annuler)
     * 
     * POST /api/applications/{id}/reset-follow-up
     */
    public function resetFollowUp(Request $request, Application $application): JsonResponse
    {
        if ($application->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Candidature non trouvée.',
            ], 404);
        }

        $application->update(['needs_follow_up' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Statut de relance réinitialisé.',
            'data' => [
                'id' => $application->id,
                'needs_follow_up' => $application->needs_follow_up,
            ],
        ]);
    }

    /**
     * Force la vérification des relances (trigger manuel)
     * 
     * POST /api/applications/check-follow-ups
     */
    public function checkFollowUps(Request $request): JsonResponse
    {
        try {
            \Artisan::call('applications:check-followups', ['--force' => true]);
            
            $output = \Artisan::output();

            return response()->json([
                'success' => true,
                'message' => 'Vérification des relances effectuée.',
                'output' => $output,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la vérification : ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Récupère les statistiques de relance
     * 
     * GET /api/applications/follow-up-stats
     */
    public function stats(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $stats = [
            'total_needing_follow_up' => Application::where('user_id', $userId)
                ->needingFollowUp()
                ->count(),
            'total_followed_up' => Application::where('user_id', $userId)
                ->where('follow_up_count', '>', 0)
                ->count(),
            'average_follow_ups' => Application::where('user_id', $userId)
                ->where('follow_up_count', '>', 0)
                ->avg('follow_up_count'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}