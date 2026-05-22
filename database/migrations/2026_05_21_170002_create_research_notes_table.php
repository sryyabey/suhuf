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
        Schema::create('research_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('sura');
            $table->unsignedInteger('aya');
            $table->unsignedInteger('word_position')->nullable();
            $table->string('type')->default('note'); // note|footnote|research
            $table->string('title');
            $table->text('content');
            $table->timestamps();

            $table->index(['user_id', 'sura', 'aya']);
            $table->index(['user_id', 'sura', 'aya', 'word_position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('research_notes');
    }
};
