<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menambah foreign key users.division_id -> divisions setelah tabel divisions ada.
 * ON DELETE RESTRICT: divisi yang dipakai tidak boleh dihapus (nonaktifkan via is_active).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('division_id')->references('id')->on('divisions')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['division_id']);
        });
    }
};
