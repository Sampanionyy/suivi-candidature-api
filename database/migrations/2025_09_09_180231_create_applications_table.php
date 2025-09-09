<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('position');
            $table->string('company');
            $table->text('job_url')->nullable();
            $table->date('applied_date');
            $table->enum('status', [
                'to_apply',
                'applied', 
                'interview',
                'rejected',
                'accepted'
            ])->default('to_apply');
            $table->datetime('interview_date')->nullable();
            $table->text('notes')->nullable();
            $table->string('cv_path')->nullable();
            $table->string('cover_letter_path')->nullable();
            $table->timestamps();

            // Index pour optimiser les requêtes
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'applied_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
