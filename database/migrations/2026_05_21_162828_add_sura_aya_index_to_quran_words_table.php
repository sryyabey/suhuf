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
        Schema::table('quran_words', function (Blueprint $table) {
            $table->index(['sura', 'aya'], 'quran_words_sura_aya_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quran_words', function (Blueprint $table) {
            $table->dropIndex('quran_words_sura_aya_idx');
        });
    }
};
