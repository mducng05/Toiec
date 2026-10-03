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
        Schema::create('passages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_part_id')->constrained()->cascadeOnDelete();
            $table->foreignId('audio_file_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title', 255)->nullable();
            $table->text('content')->nullable();
            $table->string('image_path', 500)->nullable();
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
        Schema::dropIfExists('passages');
    }
};
