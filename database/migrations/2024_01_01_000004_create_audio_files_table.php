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
        Schema::create('audio_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_part_id')->constrained()->cascadeOnDelete();
            $table->string('file_path', 500);
            $table->string('original_name', 255);
            $table->unsignedInteger('file_size')->nullable(); // bytes
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();

            $table->index('exam_part_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audio_files');
    }
};
