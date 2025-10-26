<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DocumentFactory extends Factory
{
    protected $model = Document::class;

    public function definition()
    {
        $fileName = $this->faker->word() . '.pdf';

        return [
            'user_id'  => User::factory(), 
            'name'     => $this->faker->sentence(3),
            'type'     => 'CV',
            'file_url' => '/storage/documents/' . $fileName,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
