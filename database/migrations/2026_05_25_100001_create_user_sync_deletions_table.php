<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kullanıcının sildiği kayıtları takip eder (tombstone).
 *
 * Sync sırasında diğer cihaza "bu kaydı sil" talimatı gönderilir.
 * entity_type  : 'research_note' | 'research_tag' | 'bookmark'
 * entity_uuid  : Not/etiket için UUID
 * entity_key   : Yer imi için sayfa numarası (string olarak)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_sync_deletions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('entity_type', 50);
            $table->string('entity_uuid', 36)->nullable();
            $table->string('entity_key', 255)->nullable();
            $table->timestamp('deleted_at');
            $table->timestamps();

            $table->index(['user_id', 'entity_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_sync_deletions');
    }
};
