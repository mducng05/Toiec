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
        Schema::table('attempt_results', function (Blueprint $table) {
            $table->unsignedSmallInteger('listening_score')->nullable()->after('reading_correct');
            $table->unsignedSmallInteger('reading_score')->nullable()->after('listening_score');
            $table->unsignedSmallInteger('total_score')->nullable()->after('reading_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attempt_results', function (Blueprint $table) {
            $table->dropColumn(['listening_score', 'reading_score', 'total_score']);
        });
    }
};
