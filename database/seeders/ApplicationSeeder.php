<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ApplicationSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('fr_FR');
        
        // Créer un utilisateur de test
        $testUser = User::firstOrCreate([
            'email' => 'test@jobtracker.com'
        ], [
            'name' => 'Utilisateur Test',
            'password' => bcrypt('password'),
        ]);

        // Données réalistes d'entreprises et postes
        $companies = [
            'Google', 'Microsoft', 'Apple', 'Meta', 'Amazon', 'Netflix', 'Spotify',
            'Airbnb', 'Uber', 'Tesla', 'Shopify', 'Stripe', 'Figma', 'Notion',
            'Slack', 'Discord', 'Twitter', 'LinkedIn', 'GitHub', 'GitLab',
            'Atlassian', 'Salesforce', 'Adobe', 'Canva', 'Zoom', 'Dropbox'
        ];

        $positions = [
            'Développeur Frontend React',
            'Développeur Backend Node.js',
            'Développeur Full Stack',
            'Ingénieur DevOps',
            'Product Manager',
            'UX/UI Designer',
            'Data Scientist',
            'Ingénieur Machine Learning',
            'Chef de Projet IT',
            'Consultant Technique',
            'Architecte Solutions',
            'Développeur Mobile React Native',
            'Ingénieur Cloud AWS',
            'Analyste Business Intelligence',
            'Scrum Master'
        ];

        $statuses = array_keys(Application::STATUSES);

        // Créer 50 candidatures de test
        for ($i = 0; $i < 50; $i++) {
            $status = $faker->randomElement($statuses);
            $appliedDate = $faker->dateTimeBetween('-6 months', 'now');
            
            // Si le statut est "interview", on génère une date d'entretien
            $interviewDate = null;
            if ($status === 'interview') {
                $interviewDate = $faker->dateTimeBetween('now', '+2 weeks');
            }

            Application::create([
                'user_id' => $testUser->id,
                'position' => $faker->randomElement($positions),
                'company' => $faker->randomElement($companies),
                'job_url' => $faker->optional(0.7)->url(),
                'applied_date' => $appliedDate,
                'status' => $status,
                'interview_date' => $interviewDate,
                'notes' => $faker->optional(0.4)->paragraph(2),
            ]);
        }

        // Créer quelques candidatures avec entretiens dans 3 jours (pour tester les rappels)
        for ($i = 0; $i < 3; $i++) {
            Application::create([
                'user_id' => $testUser->id,
                'position' => $faker->randomElement($positions),
                'company' => $faker->randomElement($companies),
                'job_url' => $faker->url(),
                'applied_date' => $faker->dateTimeBetween('-1 month', '-1 week'),
                'status' => 'interview',
                'interview_date' => now()->addDays(3)->setHour($faker->numberBetween(9, 17))->setMinute($faker->randomElement([0, 30])),
                'notes' => 'Entretien important avec le directeur technique. Préparer les questions sur l\'architecture.',
            ]);
        }

        $this->command->info(Application::count() . ' candidatures créées pour les tests');
        $this->command->info('Utilisateur test créé : test@jobtracker.com / password');
    }
}