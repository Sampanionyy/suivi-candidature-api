<?php

namespace Database\Factories;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    protected $model = Profile::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'photo_url' => null,
            'linkedin_url' => $this->faker->optional()->url(),
            'github_url' => $this->faker->optional()->url(),
            'portfolio_url' => $this->faker->optional()->url(),
            'summary' => $this->faker->optional()->paragraph(),
            'years_of_experience' => $this->faker->numberBetween(0, 20),
        ];
    }
}
