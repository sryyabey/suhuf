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
        if (Schema::hasTable('quran_words')) {
            return;
        }

        Schema::create('quran_words', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('aya');
            $table->unsignedInteger('sura');
            $table->unsignedInteger('position');
            $table->string('verse_key');
            $table->text('text');
            $table->string('simple');
            $table->unsignedInteger('juz');
            $table->unsignedInteger('hezb');
            $table->unsignedInteger('rub');
            $table->unsignedInteger('page');
            $table->string('class_name');
            $table->unsignedInteger('line');
            $table->string('code');
            $table->string('code_v3');
            $table->string('char_type');
            $table->string('audio');
            $table->text('translation');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quran_words');
    }
};
