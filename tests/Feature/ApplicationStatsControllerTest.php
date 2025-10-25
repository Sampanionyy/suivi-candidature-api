<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class ApplicationStatsControllerTest extends TestCase
{
    public function test_returns_stats_for_authenticated_user()
    {
        $user = User::factory()->create();

        // Générer des applications pour l'utilisateur connecté
        Application::factory()->create([
            'user_id' => $user->id,
            'status' => 'applied',
            'company' => 'Google',
            'position' => 'Développeur Backend',
            'applied_date' => Carbon::now()->subDays(2),
        ]);

        Application::factory()->create([
            'user_id' => $user->id,
            'status' => 'accepted',
            'company' => 'OpenAI',
            'position' => 'Développeur Backend',
            'applied_date' => Carbon::now()->subDays(5),
            'interview_date' => Carbon::now()->addDays(3),
        ]);

        // Autre utilisateur (ne doit pas apparaître)
        Application::factory()->create([
            'status' => 'rejected',
            'company' => 'Amazon',
            'position' => 'Designer',
            'applied_date' => Carbon::now()->subDays(10),
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/applications-stats');

        $response->assertOk()
                 ->assertJsonPath('success', true)
                 ->assertJsonStructure([
                     'data' => [
                         'byStatus',
                         'topCompanies',
                         'upcomingInterviews',
                         'totalApplications',
                         'applicationsOverTime',
                         'positions',
                     ]
                 ]);

        // Vérifier le nombre total de candidatures
        $response->assertJsonPath('data.totalApplications', 2);
    }

    public function test_can_return_monthly_applications_stats()
    {
        $user = User::factory()->create();

        Application::factory()->create([
            'user_id' => $user->id,
            'applied_date' => Carbon::now()->subMonths(1),
        ]);

        Application::factory()->create([
            'user_id' => $user->id,
            'applied_date' => Carbon::now()->subDays(10),
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/applications-stats?period=month');

        $response->assertOk()
                 ->assertJsonPath('success', true)
                 ->assertJsonStructure(['data' => ['applicationsOverTime']]);
    }

    public function test_can_return_yearly_applications_stats()
    {
        $user = User::factory()->create();

        Application::factory()->create([
            'user_id' => $user->id,
            'applied_date' => Carbon::now()->subYears(1),
        ]);

        Application::factory()->create([
            'user_id' => $user->id,
            'applied_date' => Carbon::now(),
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/applications-stats?period=year');

        $response->assertOk()
                 ->assertJsonPath('success', true)
                 ->assertJsonStructure(['data' => ['applicationsOverTime']]);
    }
}
