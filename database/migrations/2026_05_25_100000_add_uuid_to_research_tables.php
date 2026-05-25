<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('research_notes', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique()->after('id');
        });

        Schema::table('research_tags', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique()->after('id');
        });

        // Mevcut kayıtları UUID ile doldur
        DB::table('research_notes')->whereNull('uuid')->chunkById(200, function ($rows) {
            foreach ($rows as $row) {
                DB::table('research_notes')
                    ->where('id', $row->id)
                    ->update(['uuid' => (string) Str::uuid()]);
            }
        });

        DB::table('research_tags')->whereNull('uuid')->chunkById(200, function ($rows) {
            foreach ($rows as $row) {
                DB::table('research_tags')
                    ->where('id', $row->id)
                    ->update(['uuid' => (string) Str::uuid()]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('research_notes', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });

        Schema::table('research_tags', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
