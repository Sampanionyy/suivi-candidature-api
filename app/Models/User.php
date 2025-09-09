<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relations
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    // Méthodes pour les statistiques
    public function getApplicationsStatsAttribute(): array
    {
        $applications = $this->applications;
        $total = $applications->count();
        
        if ($total === 0) {
            return [
                'total' => 0,
                'response_rate' => 0,
                'by_status' => [],
                'interviews_count' => 0
            ];
        }

        $byStatus = $applications->groupBy('status')->map->count()->toArray();
        $responded = ($byStatus['interview'] ?? 0) + ($byStatus['rejected'] ?? 0) + ($byStatus['accepted'] ?? 0);
        
        return [
            'total' => $total,
            'response_rate' => $total > 0 ? round(($responded / $total) * 100, 1) : 0,
            'by_status' => $byStatus,
            'interviews_count' => $byStatus['interview'] ?? 0
        ];
    }

    public function upcomingInterviews()
    {
        return $this->applications()
            ->where('status', 'interview')
            ->where('interview_date', '>', now())
            ->orderBy('interview_date');
    }
}