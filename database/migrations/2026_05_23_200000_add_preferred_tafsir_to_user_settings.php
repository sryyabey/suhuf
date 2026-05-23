<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_settings', function (Blueprint $table) {
            $table->unsignedInteger('preferred_tafsir_id')->nullable()->after('preferred_language');
            $table->string('preferred_tafsir_name', 200)->nullable()->after('preferred_tafsir_id');
        });
    }

    public function down(): void
    {
        Schema::table('user_settings', function (Blueprint $table) {
            $table->dropColumn(['preferred_tafsir_id', 'preferred_tafsir_name']);
        });
    }
};
