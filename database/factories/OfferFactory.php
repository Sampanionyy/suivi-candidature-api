<?php

namespace Database\Factories;

use App\Models\Offer;
use App\Models\JobContractType;
use App\Models\WorkMode;
use Illuminate\Database\Eloquent\Factories\Factory;

class OfferFactory extends Factory
{
    protected $model = Offer::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->unique()->jobTitle(),
            'company' => $this->faker->company(),
            'location' => $this->faker->city(),
            'url' => $this->faker->url(),
            'description' => $this->faker->paragraph(),
            'external_id' => $this->faker->unique()->uuid(),
            'source' => $this->faker->word(),
            'company_logo_url' => $this->faker->imageUrl(100, 100),
            'salary_min' => $this->faker->numberBetween(1500, 3000),
            'salary_max' => $this->faker->numberBetween(3001, 6000),
            'is_active' => $this->faker->boolean(),
            'scraped_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'published_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'raw_data' => ['example' => $this->faker->sentence()],
            'job_contract_type_id' => JobContractType::factory(),
            'work_mode_id' => WorkMode::factory(),
        ];
    }
}
