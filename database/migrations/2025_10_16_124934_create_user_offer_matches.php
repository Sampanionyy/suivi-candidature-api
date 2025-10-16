<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_offer_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('profile_id')->constrained()->onDelete('cascade');
            $table->foreignId('offer_id')->constrained()->onDelete('cascade');
            
            $table->integer('match_score')->default(0);
            
            $table->json('matched_skills')->nullable(); 
            $table->json('missing_skills')->nullable(); 
            
            $table->enum('status', [
                'new',        
                'viewed',     
                'interested', 
                'applied',    
                'rejected',   
                'hidden'      
            ])->default('new');
            
            $table->timestamp('viewed_at')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'offer_id']); 
            $table->index(['user_id', 'status']);
            $table->index('match_score');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_offer_matches');
    }
};