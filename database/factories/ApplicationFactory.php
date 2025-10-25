<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApplicationFactory extends Factory
{
    protected $model = Application::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'position' => $this->faker->jobTitle(),
            'company' => $this->faker->company(),
            'job_url' => $this->faker->optional()->url(),
            'applied_date' => $this->faker->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
            'status' => $this->faker->randomElement(array_keys(Application::STATUSES)),
            'cv_path' => null,
            'cover_letter_path' => null,
            'interview_date' => $this->faker->optional()->dateTimeBetween('now', '+1 month'),
            'notes' => $this->faker->optional()->sentence(10),
        ];
    }
}
