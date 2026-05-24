<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hadith_books', function (Blueprint $table): void {
            $table->id();
            $table->string('source', 50)->default('kutub_al_sittah');
            $table->string('code', 80);
            $table->string('name_ar', 255)->nullable();
            $table->string('name_en', 255)->nullable();
            $table->string('name_tr', 255)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['source', 'code']);
        });

        Schema::create('hadith_chapters', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('book_id')->constrained('hadith_books')->cascadeOnDelete();
            $table->string('code', 80);
            $table->unsignedInteger('chapter_no')->nullable();
            $table->string('name_ar', 255)->nullable();
            $table->string('name_en', 255)->nullable();
            $table->string('name_tr', 255)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['book_id', 'code']);
            $table->index(['book_id', 'chapter_no']);
        });

        Schema::create('hadith_entries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('book_id')->constrained('hadith_books')->cascadeOnDelete();
            $table->foreignId('chapter_id')->nullable()->constrained('hadith_chapters')->nullOnDelete();
            $table->string('external_id', 120)->nullable();
            $table->unsignedInteger('hadith_no')->nullable();
            $table->string('grade', 120)->nullable();
            $table->text('arabic_text')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['book_id', 'external_id']);
            $table->index(['book_id', 'hadith_no']);
        });

        Schema::create('hadith_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('hadith_id')->constrained('hadith_entries')->cascadeOnDelete();
            $table->string('language', 8);
            $table->text('title')->nullable();
            $table->longText('text');
            $table->text('narrator')->nullable();
            $table->timestamps();

            $table->unique(['hadith_id', 'language']);
            $table->index('language');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hadith_translations');
        Schema::dropIfExists('hadith_entries');
        Schema::dropIfExists('hadith_chapters');
        Schema::dropIfExists('hadith_books');
    }
};
