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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_part_id')->constrained()->cascadeOnDelete();
            $table->foreignId('passage_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('audio_file_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('question_number');
            $table->text('content')->nullable();
            $table->string('image_path', 500)->nullable();
            $table->string('question_type', 50)->default('single_choice');
            $table->text('explanation')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();

            $table->unique(['exam_part_id', 'question_number']);
            $table->index('passage_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
