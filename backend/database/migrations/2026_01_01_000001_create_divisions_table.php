<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Divisi perusahaan (tabel referensi). Dipisah dari tickets agar tidak ada
 * duplikasi/salah ketik dan bisa ditambah tanpa deploy. Lihat docs/06-database-erd.md.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('divisions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('divisions');
    }
};
