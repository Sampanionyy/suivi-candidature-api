<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class FollowUpReminder extends Notification
{
    // Notification synchrone - pas de queue

    public function __construct(
        public Application $application
    ) {}

    /**
     * Canaux de notification (database pour les notifs in-app)
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Format de la notification en base de données
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'follow_up_reminder',
            'application_id' => $this->application->id,
            'position' => $this->application->position,
            'company' => $this->application->company,
            'applied_date' => $this->application->applied_date,
            'last_follow_up_date' => $this->application->last_follow_up_date,
            'follow_up_count' => $this->application->follow_up_count,
            'days_since_last_contact' => $this->getDaysSinceLastContact(),
            'message' => $this->getMessage(),
        ];
    }

    /**
     * Calcule le nombre de jours depuis le dernier contact
     */
    private function getDaysSinceLastContact(): int
    {
        $lastContactDate = $this->application->last_follow_up_date 
            ?? $this->application->applied_date;
        
        return now()->diffInDays($lastContactDate);
    }

    /**
     * Génère le message de la notification
     */
    private function getMessage(): string
    {
        $days = $this->getDaysSinceLastContact();
        $company = $this->application->company;
        $position = $this->application->position;
        
        if ($this->application->follow_up_count === 0) {
            return "Il est temps de relancer {$company} pour le poste de {$position} (candidature envoyée il y a {$days} jours)";
        }
        
        return "Nouvelle relance recommandée pour {$company} - {$position} ({$days} jours depuis la dernière relance)";
    }
}