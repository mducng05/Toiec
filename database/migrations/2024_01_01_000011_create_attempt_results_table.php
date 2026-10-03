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
        Schema::create('attempt_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_attempt_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedInteger('total_questions');
            $table->unsignedInteger('correct_answers');
            $table->unsignedInteger('wrong_answers');
            $table->unsignedInteger('unanswered');
            $table->unsignedInteger('listening_correct')->default(0);
            $table->unsignedInteger('reading_correct')->default(0);
            $table->decimal('accuracy_percentage', 5, 2);
            $table->json('part_results')->nullable();
            $table->timestamp('scored_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attempt_results');
    }
};
