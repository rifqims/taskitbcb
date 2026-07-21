<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Rating kepuasan client setelah tiket selesai (bahan evaluasi performa IT). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->unique()->constrained('tickets')->cascadeOnDelete();
            $table->foreignId('rated_by')->constrained('users')->restrictOnDelete();
            $table->unsignedTinyInteger('score');   // 1..5
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE ticket_ratings ADD CONSTRAINT chk_rating_score CHECK (score >= 1 AND score <= 5)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_ratings');
    }
};
