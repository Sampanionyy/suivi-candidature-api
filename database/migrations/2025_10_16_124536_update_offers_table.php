<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            // CRITIQUES - Pour éviter les doublons et tracking
            $table->string('external_id')->nullable()->unique()->after('id'); // ID de WTTJ
            $table->string('source')->default('manual')->after('external_id'); // 'wttj', 'manual', 'linkedin', etc.
            
            // IMPORTANTES - Enrichissement des données
            $table->string('company_logo_url')->nullable()->after('company');
            $table->integer('salary_min')->nullable()->after('description');
            $table->integer('salary_max')->nullable()->after('salary_min');
            $table->string('salary_currency')->default('EUR')->after('salary_max');
            
            // SCRAPING - Gestion des offres scrapées
            $table->boolean('is_active')->default(true)->after('salary_currency'); // Offre encore dispo ?
            $table->timestamp('scraped_at')->nullable()->after('is_active'); // Date du scraping
            $table->timestamp('published_at')->nullable()->after('scraped_at'); // Date de publication WTTJ
            $table->json('raw_data')->nullable()->after('published_at'); // Données brutes WTTJ
            
            // INDEX pour les requêtes
            $table->index(['source', 'is_active']);
            $table->index('scraped_at');
            $table->index('external_id');
        });
    }

    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropIndex(['source', 'is_active']);
            $table->dropIndex(['scraped_at']);
            $table->dropIndex(['external_id']);
            
            $table->dropColumn([
                'external_id',
                'source',
                'company_logo_url',
                'salary_min',
                'salary_max',
                'salary_currency',
                'is_active',
                'scraped_at',
                'published_at',
                'raw_data',
            ]);
        });
    }
};