<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    public function check()
    {
        try {
            // Vérifier la connexion à la base de données
            DB::connection()->getPdo();
            
            return response()->json([
                'status' => 'healthy',
                'service' => 'api',
                'database' => 'connected',
                'timestamp' => now()->toIso8601String()
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'unhealthy',
                'service' => 'api',
                'database' => 'disconnected',
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String()
            ], 503);
        }
    }
}