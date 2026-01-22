<?php

namespace App\Console\Commands;

use App\Models\Application;
use App\Notifications\FollowUpReminder;
use Illuminate\Console\Command;

class CheckFollowUps extends Command
{
    /**
     * Signature de la commande
     */
    protected $signature = 'applications:check-followups 
                            {--force : Force la vérification même si déjà notifié aujourd\'hui}';

    /**
     * Description de la commande
     */
    protected $description = 'Vérifie les candidatures qui nécessitent une relance (tous les 3 jours)';

    /**
     * Configuration des règles de relance
     */
    private const DAYS_BEFORE_FIRST_FOLLOW_UP = 3;
    private const DAYS_BETWEEN_FOLLOW_UPS = 3;
    private const MAX_FOLLOW_UPS = 3; // Arrête après 3 relances

    /**
     * Exécute la commande
     */
    public function handle(): int
    {
        $this->info('🔍 Vérification des candidatures à relancer...');
        
        // Récupère les candidatures en attente de réponse
        $applications = Application::whereIn('status', ['applied', 'interview'])
            ->where('needs_follow_up', false) // Évite de notifier plusieurs fois
            ->get();

        $toFollowUp = collect();

        foreach ($applications as $application) {
            if ($this->shouldFollowUp($application)) {
                $toFollowUp->push($application);
            }
        }

        if ($toFollowUp->isEmpty()) {
            $this->info('✅ Aucune candidature à relancer pour le moment.');
            return Command::SUCCESS;
        }

        $this->info("📬 {$toFollowUp->count()} candidature(s) à relancer trouvée(s).");

        // Envoie les notifications
        foreach ($toFollowUp as $application) {
            $this->sendFollowUpNotification($application);
            
            $this->line("  → {$application->company} - {$application->position}");
        }

        $this->newLine();
        $this->info('✅ Notifications envoyées avec succès !');

        return Command::SUCCESS;
    }

    /**
     * Détermine si une candidature doit être relancée
     */
    private function shouldFollowUp(Application $application): bool
    {
        // Ne pas relancer si déjà trop de relances
        if ($application->follow_up_count >= self::MAX_FOLLOW_UPS) {
            return false;
        }

        // Calcule la date du dernier contact
        $lastContactDate = $application->last_follow_up_date 
            ?? $application->applied_date;

        if (!$lastContactDate) {
            return false;
        }

        // Calcule les jours écoulés
        $daysSinceLastContact = now()->diffInDays($lastContactDate);
        $daysSinceLastContact = abs($daysSinceLastContact);

        // Première relance : après X jours de la candidature
        if ($application->follow_up_count === 0) {
            return $daysSinceLastContact >= self::DAYS_BEFORE_FIRST_FOLLOW_UP;
        }

        // Relances suivantes : tous les Y jours
        return $daysSinceLastContact >= self::DAYS_BETWEEN_FOLLOW_UPS;
    }

    /**
     * Envoie la notification de relance
     */
    private function sendFollowUpNotification(Application $application): void
    {
        try {
            // Marque comme "à relancer"
            $application->update(['needs_follow_up' => true]);

            // Envoie la notification à l'utilisateur
            $application->user->notify(new FollowUpReminder($application));

        } catch (\Exception $e) {
            $this->error("Erreur lors de l'envoi de la notification : {$e->getMessage()}");
        }
    }
}