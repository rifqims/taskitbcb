<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel inti tiket. Lihat docs/06-database-erd.md untuk normalisasi, index & FK.
 * - priority_weight disimpan (turunan) demi sorting antrian yang ter-index (BR-2).
 * - sender_name sengaja denormalisasi (bisa berbeda dari akun pembuat).
 * - "Terlambat" dihitung dari sla_due_at, bukan kolom status.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();          // TKT-YYYYMM-NNNNNN (BR-1)
            $table->string('title');
            $table->text('description');

            $table->foreignId('division_id')->constrained('divisions')->restrictOnDelete();
            $table->string('sender_name');
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();

            $table->string('priority')->default('medium');       // enum Priority
            $table->unsignedTinyInteger('priority_weight')->default(2);
            $table->string('status')->default('baru');           // enum TicketStatus
            $table->unsignedTinyInteger('progress')->default(0); // 0..100 (kelipatan 25)
            $table->string('location')->nullable();

            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('deadline')->nullable();           // deadline yang diminta client
            $table->timestamp('sla_due_at')->nullable();         // batas SLA terhitung
            $table->timestamp('started_at')->nullable();         // saat diambil teknisi
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Index untuk dashboard & antrian
            $table->index('status');
            $table->index(['priority_weight', 'created_at']);    // sorting antrian (BR-2)
            $table->index('assigned_to');
            $table->index('sla_due_at');
        });

        // Batasi progress 0..100. CHECK didukung PostgreSQL (driver utama);
        // dilewati di SQLite (test in-memory) agar migrasi tetap lintas driver.
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE tickets ADD CONSTRAINT chk_tickets_progress CHECK (progress >= 0 AND progress <= 100)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
