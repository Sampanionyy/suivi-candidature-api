<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'position',
        'company',
        'job_url',
        'applied_date',
        'status',
        'interview_date',
        'notes',
        'cv_path',
        'cover_letter_path'
    ];

    protected $casts = [
        'applied_date' => 'date',
        'interview_date' => 'datetime'
    ];

    public const STATUSES = [
        'to_apply' => 'À postuler',
        'applied' => 'Postulé',
        'interview' => 'Entretien',
        'rejected' => 'Refusé',
        'accepted' => 'Accepté'
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Accesseurs
    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getCvUrlAttribute(): ?string
    {
        return $this->cv_path ? asset('storage/' . $this->cv_path) : null;
    }

    public function getCoverLetterUrlAttribute(): ?string
    {
        return $this->cover_letter_path ? asset('storage/' . $this->cover_letter_path) : null;
    }

    // Scopes pour filtrer
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByCompany($query, string $company)
    {
        return $query->where('company', 'like', '%' . $company . '%');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('interview_date', '>', now());
    }

    public function scopeInterviewsThisWeek($query)
    {
        return $query->whereBetween('interview_date', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    // Méthode pour les rappels d'entretien
    public function needsInterviewReminder(): bool
    {
        if (!$this->interview_date) {
            return false;
        }

        $daysDiff = Carbon::parse($this->interview_date)->diffInDays(now(), false);
        
        // Envoyer rappel 3 jours avant
        return $daysDiff === -3;
    }
}