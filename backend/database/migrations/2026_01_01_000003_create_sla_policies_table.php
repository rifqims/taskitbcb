<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kebijakan SLA per prioritas (FR-9). Nilai dapat diubah Admin tanpa deploy.
 * resolution_minutes = target waktu penyelesaian sejak tiket dibuat.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sla_policies', function (Blueprint $table) {
            $table->id();
            $table->string('priority')->unique();      // low | medium | high | urgent
            $table->unsignedInteger('resolution_minutes');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sla_policies');
    }
};
