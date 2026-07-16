<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_invites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('code', 64)->unique();
            $table->unsignedInteger('max_uses')->default(1);
            $table->unsignedInteger('used_count')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('referred_by_user_id')
                ->nullable()
                ->after('remember_token')
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('used_invite_id')
                ->nullable()
                ->after('referred_by_user_id')
                ->constrained('user_invites')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('used_invite_id');
            $table->dropConstrainedForeignId('referred_by_user_id');
        });

        Schema::dropIfExists('user_invites');
    }
};
