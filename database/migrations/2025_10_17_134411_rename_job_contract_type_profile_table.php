<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécute la migration.
     */
    public function up(): void
    {
        Schema::rename('job_contract_type_profile', 'job_contract_type_profiles');
    }

    /**
     * Annule la migration.
     */
    public function down(): void
    {
        Schema::rename('job_contract_type_profiles', 'job_contract_type_profile');
    }
};
