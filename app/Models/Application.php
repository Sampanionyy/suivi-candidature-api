<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'cover_letter_path',
        'last_follow_up_date',
        'follow_up_count',
        'needs_follow_up',
    ];

    protected $casts = [
        'applied_date' => 'date',
        'interview_date' => 'datetime',
        'last_follow_up_date' => 'date',
        'follow_up_count' => 'integer',
        'needs_follow_up' => 'boolean',
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

    public function scopeNeedingFollowUp($query)
    {
        return $query->where('needs_follow_up', true)
                     ->whereIn('status', ['applied', 'interview']);
    }

    // Méthodes métier
    public function needsInterviewReminder(): bool
    {
        if (!$this->interview_date) {
            return false;
        }

        $daysDiff = Carbon::parse($this->interview_date)->diffInDays(now(), false);
        
        // Envoyer rappel 3 jours avant
        return $daysDiff === -3;
    }

    public function needsFollowUp(): bool
    {
        if (!in_array($this->status, ['applied', 'interview'])) {
            return false;
        }

        if ($this->follow_up_count >= 3) {
            return false;
        }

        $lastContactDate = $this->last_follow_up_date ?? $this->applied_date;
        
        if (!$lastContactDate) {
            return false;
        }

        $daysSinceLastContact = abs(now()->diffInDays($lastContactDate));

        return $daysSinceLastContact >= 3;
    }

    public function daysSinceLastContact(): int
    {
        $lastContactDate = $this->last_follow_up_date ?? $this->applied_date;
        
        return $lastContactDate ? abs(now()->diffInDays($lastContactDate)) : 0;
    }

    public function markFollowUpSent(): void
    {
        $this->update([
            'last_follow_up_date' => now(),
            'follow_up_count' => $this->follow_up_count + 1,
            'needs_follow_up' => false,
        ]);
    }
}