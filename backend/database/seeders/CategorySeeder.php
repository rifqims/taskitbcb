<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // name => icon (lucide) — sesuai FR-6
        $categories = [
            'Hardware' => 'cpu',
            'Software' => 'app-window',
            'Printer' => 'printer',
            'Internet' => 'wifi',
            'Network' => 'network',
            'Email' => 'mail',
            'Website' => 'globe',
            'Server' => 'server',
            'CCTV' => 'cctv',
            'Aplikasi' => 'layout-grid',
            'Lainnya' => 'circle-help',
        ];

        foreach ($categories as $name => $icon) {
            Category::firstOrCreate(['name' => $name], ['icon' => $icon, 'is_active' => true]);
        }
    }
}
