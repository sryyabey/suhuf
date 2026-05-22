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
        Schema::create('research_note_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('research_note_id')->constrained()->cascadeOnDelete();
            $table->foreignId('research_tag_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['research_note_id', 'research_tag_id'], 'research_note_tag_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('research_note_tag');
    }
};
