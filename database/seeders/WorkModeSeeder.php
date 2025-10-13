<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\WorkMode;
use Carbon\Carbon;

class WorkModeSeeder extends Seeder
{
    public function run(): void
    {
        $workModes = [
            'Présentiel',
            'Télétravail',
            'Hybride',
            'Freelance sur site',
            'Freelance à distance',
        ];

        foreach ($workModes as $mode) {
            WorkMode::firstOrCreate(['name' => $mode]);
        }
    }
}