<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Application;
use Illuminate\Support\Facades\Mail;
use App\Mail\InterviewReminder;

class SendInterviewReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'interviews:send-reminders {--days=* : Jours spécifiques (ex: --days=1 --days=2 --days=3)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoie des rappels d\'entretien pour les candidatures dans 1, 2 ou 3 jours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Recherche des entretiens à venir...');

        // Définir les jours par défaut ou utiliser ceux spécifiés
        $days = $this->option('days') ?: [1, 2, 3];
        
        $totalSent = 0;
        $totalErrors = 0;

        foreach ($days as $dayCount) {
            $this->processInterviewsForDay($dayCount, $totalSent, $totalErrors);
        }

        $this->displaySummary($totalSent, $totalErrors);

        return self::SUCCESS;
    }

    /**
     * Traite les entretiens pour un jour spécifique
     */
    private function processInterviewsForDay(int $dayCount, int &$totalSent, int &$totalErrors): void
    {
        $targetDate = Carbon::now()->addDays($dayCount);
        
        $this->line("\n📅 Entretiens dans {$dayCount} jour(s) ({$targetDate->format('d/m/Y')}) :");

        $applications = Application::where('status', 'interview')
            ->whereDate('interview_date', $targetDate->toDateString())
            ->with('user')
            ->get();

        if ($applications->isEmpty()) {
            $this->line("   ✅ Aucun entretien trouvé");
            return;
        }

        $this->line("   📧 {$applications->count()} rappel(s) à envoyer...");

        $sent = 0;
        $errors = 0;

        foreach ($applications as $application) {
            try {
                // Passer le nombre de jours restants au mail
                Mail::to($application->user->email)
                    ->send(new InterviewReminder($application, $dayCount));
                
                $this->line("   ✉️  Rappel envoyé à {$application->user->name} pour {$application->company}");
                $sent++;
                
            } catch (\Exception $e) {
                $this->error("   ❌ Erreur pour {$application->user->name}: " . $e->getMessage());
                $errors++;
            }
        }

        $totalSent += $sent;
        $totalErrors += $errors;

        $this->line("   📊 Résultat : {$sent} envoyé(s)" . ($errors > 0 ? ", {$errors} erreur(s)" : ""));
    }

    /**
     * Affiche le résumé final
     */
    private function displaySummary(int $totalSent, int $totalErrors): void
    {
        $this->info("\n" . str_repeat("=", 50));
        $this->info("📈 RÉSUMÉ GLOBAL :");
        $this->info("   • Total rappels envoyés : {$totalSent}");
        
        if ($totalErrors > 0) {
            $this->warn("   • Total erreurs : {$totalErrors}");
        }
        
        if ($totalSent === 0 && $totalErrors === 0) {
            $this->comment("   • Aucun rappel à envoyer aujourd'hui");
        }
        
        $this->info(str_repeat("=", 50));
    }
}