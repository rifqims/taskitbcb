<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Urutan penting: data referensi dulu (divisi, kategori, SLA), lalu user,
     * baru contoh tiket. Lihat docs/06-database-erd.md §5.
     */
    public function run(): void
    {
        $this->call([
            DivisionSeeder::class,
            CategorySeeder::class,
            SlaPolicySeeder::class,
            UserSeeder::class,
            TicketSeeder::class,
        ]);
    }
}
