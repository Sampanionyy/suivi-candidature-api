<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\ApplicationStatsController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\SkillCategoryController;
use App\Http\Controllers\Api\SkillController;
use App\Http\Controllers\Api\WorkModeController;
use App\Http\Controllers\Api\JobContractTypeController;
use App\Http\Controllers\Api\FollowUpController;
use App\Http\Controllers\Api\OfferController;
use App\Http\Controllers\HealthController;
use App\Models\Application;

// ==========================================
// Routes publiques (sans authentification)
// ==========================================
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::get('/health', [HealthController::class, 'check']);

// ==========================================
// Routes protégées (avec authentification)
// ==========================================
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth user
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'user']);
    });

    // Routes de follow-up
    Route::get('applications/follow-ups', [FollowUpController::class, 'index']);
    Route::post('applications/check-follow-ups', [FollowUpController::class, 'checkFollowUps']);
    Route::get('applications/follow-up-stats', [FollowUpController::class, 'stats']);
    
    // Routes stats et interviews
    Route::get('applications-stats', [ApplicationStatsController::class, 'index']);
    Route::get('applications-interviews', [ApplicationController::class, 'interviews']);
    
    // Routes avec paramètre {application}
    Route::post('applications/{application}/mark-follow-up-sent', [FollowUpController::class, 'markFollowUpSent']);
    Route::post('applications/{application}/reset-follow-up', [FollowUpController::class, 'resetFollowUp']);
    Route::post('applications/{application}/upload-cv', [ApplicationController::class, 'uploadCV']);
    Route::post('applications/{application}/upload-cover-letter', [ApplicationController::class, 'uploadCoverLetter']);
    Route::patch('applications/{application}/status', [ApplicationController::class, 'updateStatus']);
    
    // CRUD Applications
    Route::apiResource('applications', ApplicationController::class);

    // ==========================================
    // Profil
    // ==========================================
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto']);
    Route::delete('/profile/photo', [ProfileController::class, 'deletePhoto']);

    // ==========================================
    // Documents
    // ==========================================
    Route::get('/documents', [DocumentController::class, 'index']);
    Route::post('/documents', [DocumentController::class, 'store']);
    Route::delete('/documents/{id}', [DocumentController::class, 'destroy']);

    // ==========================================
    // Resources (Skills, Contract Types, etc.)
    // ==========================================
    Route::apiResource('/skill-categories', SkillCategoryController::class);
    Route::apiResource('/skills', SkillController::class);
    Route::apiResource('/job-contract-types', JobContractTypeController::class);
    Route::apiResource('/work-modes', WorkModeController::class);
    Route::apiResource('/offers', OfferController::class);

    // ==========================================
    // Notifications
    // ==========================================
    Route::prefix('notifications')->group(function () {
        // Liste des notifications non lues
        Route::get('/unread', function () {
            return response()->json([
                'success' => true,
                'count' => auth()->user()->unreadNotifications->count(),
                'data' => auth()->user()->unreadNotifications,
            ]);
        });
        
        // Toutes les notifications
        Route::get('/', function () {
            return response()->json([
                'success' => true,
                'data' => auth()->user()->notifications()->paginate(20),
            ]);
        });
        
        // Marquer une notification comme lue
        Route::post('/{id}/mark-as-read', function ($id) {
            $notification = auth()->user()->notifications()->find($id);
            
            if ($notification) {
                $notification->markAsRead();
                return response()->json(['success' => true]);
            }
            
            return response()->json(['success' => false], 404);
        });
        
        // Marquer toutes les notifications comme lues
        Route::post('/mark-all-as-read', function () {
            auth()->user()->unreadNotifications->markAsRead();
            return response()->json(['success' => true]);
        });
    });
});