<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SkillCategory;

class SkillCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Frontend',
            'Backend',
            'Mobile',
            'DevOps',
            'Database',
            'Design',
            'AI / Machine Learning',
            'Testing',
        ];

        foreach ($categories as $name) {
            SkillCategory::firstOrCreate(['name' => $name]);
        }
    }
}
