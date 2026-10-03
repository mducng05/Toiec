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
        Schema::create('exam_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('part_number'); // 1-7
            $table->string('title', 100)->nullable();
            $table->enum('section', ['listening', 'reading']);
            $table->text('instructions')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();

            $table->unique(['exam_id', 'part_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_parts');
    }
};
