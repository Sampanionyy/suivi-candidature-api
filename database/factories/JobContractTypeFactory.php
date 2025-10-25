<?php

namespace Database\Factories;

use App\Models\JobContractType;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobContractTypeFactory extends Factory
{
    protected $model = JobContractType::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
        ];
    }
}
