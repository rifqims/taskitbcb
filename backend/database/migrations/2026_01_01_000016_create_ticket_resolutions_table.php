<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Berita acara penyelesaian tiket (FR-13). Satu tiket punya paling banyak satu resolusi.
 * Jika outcome=rejected, kolom reason/berita_acara/recommendation wajib diisi (divalidasi di service).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_resolutions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->unique()->constrained('tickets')->cascadeOnDelete();
            $table->string('outcome');           // completed | rejected
            $table->text('reason')->nullable();
            $table->text('berita_acara')->nullable();
            $table->text('recommendation')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_resolutions');
    }
};
