<?php

namespace Database\Seeders;

use App\Models\Division;
use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    public function run(): void
    {
        $divisions = [
            'IT', 'Finance', 'HRD', 'Sales', 'Marketing',
            'Operasional', 'Produksi', 'Direksi', 'Umum',
        ];

        foreach ($divisions as $name) {
            Division::firstOrCreate(
                ['name' => $name],
                ['code' => strtoupper(substr($name, 0, 3)), 'is_active' => true],
            );
        }
    }
}
