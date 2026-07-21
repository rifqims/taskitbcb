<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Mention @user di dalam pesan chat (FR-16, memicu notifikasi mention FR-21). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('message_mentions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained('ticket_messages')->cascadeOnDelete();
            $table->foreignId('mentioned_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['message_id', 'mentioned_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_mentions');
    }
};
