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
        Schema::create('verse_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('sura');
            $table->unsignedSmallInteger('aya');
            $table->string('meal_key');
            $table->string('language', 8)->default('tr');
            $table->text('text');
            $table->timestamps();

            $table->index(['sura', 'aya']);
            $table->unique(['sura', 'aya', 'meal_key', 'language'], 'verse_meal_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verse_translations');
    }
};
