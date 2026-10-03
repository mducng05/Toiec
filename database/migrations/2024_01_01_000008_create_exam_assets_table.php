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
        Schema::create('exam_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['exam_pdf', 'answer_pdf', 'image', 'other']);
            $table->string('file_path', 500);
            $table->string('original_name', 255);
            $table->unsignedInteger('file_size')->nullable(); // bytes
            $table->string('mime_type', 100)->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();

            $table->index(['exam_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_assets');
    }
};
