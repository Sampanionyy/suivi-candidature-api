<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class ApplicationStatsController extends Controller
{
    public function index(Request $request)
    {
        try {
            $userId = Auth::id();
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié.'
                ], 401);
            }

            // Candidatures par statut
            $byStatus = Application::select('status', DB::raw('count(*) as total'))
                ->where('user_id', $userId)
                ->groupBy('status')
                ->get();

            // Top entreprises ciblées
            $topCompanies = Application::select('company', DB::raw('count(*) as total'))
                ->where('user_id', $userId)
                ->groupBy('company')
                ->orderByDesc('total')
                ->limit(10)
                ->get();

            // Entretiens à préparer (date future)
            $upcomingInterviews = Application::where('user_id', $userId)
                ->whereNotNull('interview_date')
                ->whereDate('interview_date', '>=', Carbon::today())
                ->orderBy('interview_date')
                ->get();

            // Candidatures envoyées - global et par période
            $totalApplications = Application::where('user_id', $userId)->count();

            $period = $request->query('period', 'week'); //week' | 'month' | 'year'

            $applicationsOverTime = match($period) {
                'month' => Application::select(
                        DB::raw('YEAR(applied_date) as year'), 
                        DB::raw('MONTH(applied_date) as month'), 
                        DB::raw('count(*) as total')
                    )
                    ->where('user_id', $userId)
                    ->groupBy('year', 'month')
                    ->orderBy('year')
                    ->orderBy('month')
                    ->get(),
                'year' => Application::select(
                        DB::raw('YEAR(applied_date) as year'), 
                        DB::raw('count(*) as total')
                    )
                    ->where('user_id', $userId)
                    ->groupBy('year')
                    ->orderBy('year')
                    ->get(),
                default => Application::select(
                        DB::raw('YEAR(applied_date) as year'), 
                        DB::raw('WEEK(applied_date) as week'), 
                        DB::raw('count(*) as total')
                    )
                    ->where('user_id', $userId)
                    ->groupBy('year', 'week')
                    ->orderBy('year')
                    ->orderBy('week')
                    ->get(),
            };

            // Répartition des postes
            $positions = Application::select('position', DB::raw('count(*) as total'))
                ->where('user_id', $userId)
                ->groupBy('position')
                ->orderByDesc('total')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'byStatus' => $byStatus,
                    'topCompanies' => $topCompanies,
                    'upcomingInterviews' => $upcomingInterviews,
                    'totalApplications' => $totalApplications,
                    'applicationsOverTime' => $applicationsOverTime,
                    'positions' => $positions,
                ]
            ], 200);

        } catch (Exception $e) {
            \Log::error('Erreur récupération stats applications: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Impossible de récupérer les statistiques.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}