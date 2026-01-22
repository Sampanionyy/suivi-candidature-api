<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->date('last_follow_up_date')->nullable()->after('applied_date');
            $table->integer('follow_up_count')->default(0)->after('last_follow_up_date');
            $table->boolean('needs_follow_up')->default(false)->after('follow_up_count');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['last_follow_up_date', 'follow_up_count', 'needs_follow_up']);
        });
    }
};