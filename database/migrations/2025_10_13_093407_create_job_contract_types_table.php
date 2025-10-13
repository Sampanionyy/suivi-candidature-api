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
        Schema::create('job_contract_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // CDI, CDD, Internship, Alternance
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_contract_types');
    }
};
