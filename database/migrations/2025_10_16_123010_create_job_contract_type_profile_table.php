<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('job_contract_type_profile', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_contract_type_id');
            $table->unsignedBigInteger('profile_id'); // ID de la table profiles
            $table->timestamps();

            // Foreign keys
            $table->foreign('job_contract_type_id')
                  ->references('id')
                  ->on('job_contract_types')
                  ->onDelete('cascade');

            $table->foreign('profile_id')
                  ->references('id')
                  ->on('profiles')
                  ->onDelete('cascade');

            // Pour éviter les doublons
            $table->unique(['job_contract_type_id', 'profile_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_contract_type_profile');
    }
};
