<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\JobContractType;
use Carbon\Carbon;

class JobContractTypeSeeder extends Seeder
{
    public function run(): void
    {
        $contractTypes = [
            'CDI',
            'CDD',
            'Freelance',
            'Stage',
            'Alternance',
            'Intérim',
            'Contrat pro',
            'VIE',
        ];

        foreach ($contractTypes as $type) {
            JobContractType::firstOrCreate([
                'name' => $type
            ]);
        }
    }
}