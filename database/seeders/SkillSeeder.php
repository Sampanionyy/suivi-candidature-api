<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Skill;
use Carbon\Carbon;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            // Développement Web (category_id: 1)
            ['name' => 'PHP', 'skill_category_id' => 1],
            ['name' => 'Laravel', 'skill_category_id' => 1],
            ['name' => 'Symfony', 'skill_category_id' => 1],
            ['name' => 'JavaScript', 'skill_category_id' => 1],
            ['name' => 'TypeScript', 'skill_category_id' => 1],
            ['name' => 'React', 'skill_category_id' => 1],
            ['name' => 'Vue.js', 'skill_category_id' => 1],
            ['name' => 'Angular', 'skill_category_id' => 1],
            ['name' => 'Node.js', 'skill_category_id' => 1],
            ['name' => 'HTML/CSS', 'skill_category_id' => 1],
            ['name' => 'Tailwind CSS', 'skill_category_id' => 1],
            ['name' => 'Bootstrap', 'skill_category_id' => 1],
            
            // Développement Mobile (category_id: 2)
            ['name' => 'React Native', 'skill_category_id' => 2],
            ['name' => 'Flutter', 'skill_category_id' => 2],
            ['name' => 'Swift', 'skill_category_id' => 2],
            ['name' => 'Kotlin', 'skill_category_id' => 2],
            ['name' => 'iOS Development', 'skill_category_id' => 2],
            ['name' => 'Android Development', 'skill_category_id' => 2],
            
            // Data Science & IA (category_id: 3)
            ['name' => 'Python', 'skill_category_id' => 3],
            ['name' => 'Machine Learning', 'skill_category_id' => 3],
            ['name' => 'Deep Learning', 'skill_category_id' => 3],
            ['name' => 'TensorFlow', 'skill_category_id' => 3],
            ['name' => 'PyTorch', 'skill_category_id' => 3],
            ['name' => 'Data Analysis', 'skill_category_id' => 3],
            ['name' => 'Pandas', 'skill_category_id' => 3],
            ['name' => 'NumPy', 'skill_category_id' => 3],
            ['name' => 'R', 'skill_category_id' => 3],
            ['name' => 'SQL', 'skill_category_id' => 3],
            
            // DevOps & Cloud (category_id: 4)
            ['name' => 'Docker', 'skill_category_id' => 4],
            ['name' => 'Kubernetes', 'skill_category_id' => 4],
            ['name' => 'AWS', 'skill_category_id' => 4],
            ['name' => 'Azure', 'skill_category_id' => 4],
            ['name' => 'Google Cloud', 'skill_category_id' => 4],
            ['name' => 'CI/CD', 'skill_category_id' => 4],
            ['name' => 'Jenkins', 'skill_category_id' => 4],
            ['name' => 'GitLab CI', 'skill_category_id' => 4],
            ['name' => 'Terraform', 'skill_category_id' => 4],
            ['name' => 'Ansible', 'skill_category_id' => 4],
            
            // Design & UX/UI (category_id: 5)
            ['name' => 'Figma', 'skill_category_id' => 5],
            ['name' => 'Adobe XD', 'skill_category_id' => 5],
            ['name' => 'Sketch', 'skill_category_id' => 5],
            ['name' => 'Photoshop', 'skill_category_id' => 5],
            ['name' => 'Illustrator', 'skill_category_id' => 5],
            ['name' => 'UI Design', 'skill_category_id' => 5],
            ['name' => 'UX Research', 'skill_category_id' => 5],
            ['name' => 'Prototyping', 'skill_category_id' => 5],
            
            // Cybersécurité (category_id: 6)
            ['name' => 'Pentest', 'skill_category_id' => 6],
            ['name' => 'Sécurité réseau', 'skill_category_id' => 6],
            ['name' => 'Cryptographie', 'skill_category_id' => 6],
            ['name' => 'OWASP', 'skill_category_id' => 6],
            ['name' => 'Firewall', 'skill_category_id' => 6],
            ['name' => 'Ethical Hacking', 'skill_category_id' => 6],
            
            // Base de données (category_id: 7)
            ['name' => 'MySQL', 'skill_category_id' => 7],
            ['name' => 'PostgreSQL', 'skill_category_id' => 7],
            ['name' => 'MongoDB', 'skill_category_id' => 7],
            ['name' => 'Redis', 'skill_category_id' => 7],
            ['name' => 'Oracle', 'skill_category_id' => 7],
            ['name' => 'SQL Server', 'skill_category_id' => 7],
            ['name' => 'Elasticsearch', 'skill_category_id' => 7],
            
            // Gestion de projet (category_id: 8)
            ['name' => 'Scrum', 'skill_category_id' => 8],
            ['name' => 'Agile', 'skill_category_id' => 8],
            ['name' => 'Kanban', 'skill_category_id' => 8],
            ['name' => 'Jira', 'skill_category_id' => 8],
            ['name' => 'Trello', 'skill_category_id' => 8],
            ['name' => 'Monday.com', 'skill_category_id' => 8],
            ['name' => 'Management d\'équipe', 'skill_category_id' => 8],
            
            // Marketing Digital (category_id: 9)
            ['name' => 'SEO', 'skill_category_id' => 9],
            ['name' => 'SEA', 'skill_category_id' => 9],
            ['name' => 'Google Ads', 'skill_category_id' => 9],
            ['name' => 'Facebook Ads', 'skill_category_id' => 9],
            ['name' => 'Google Analytics', 'skill_category_id' => 9],
            ['name' => 'Content Marketing', 'skill_category_id' => 9],
            ['name' => 'Email Marketing', 'skill_category_id' => 9],
            ['name' => 'Social Media', 'skill_category_id' => 9],
            
            // Soft Skills (category_id: 10)
            ['name' => 'Communication', 'skill_category_id' => 10],
            ['name' => 'Leadership', 'skill_category_id' => 10],
            ['name' => 'Travail d\'équipe', 'skill_category_id' => 10],
            ['name' => 'Résolution de problèmes', 'skill_category_id' => 10],
            ['name' => 'Créativité', 'skill_category_id' => 10],
            ['name' => 'Adaptabilité', 'skill_category_id' => 10],
            ['name' => 'Gestion du temps', 'skill_category_id' => 10],
            ['name' => 'Esprit critique', 'skill_category_id' => 10],
        ];

        foreach ($skills as $skill) {
            Skill::firstOrCreate(['name' => $skill['name'], 'skill_category_id' => $skill['skill_category_id']]);
        }
    }
}