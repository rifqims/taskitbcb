<?php

namespace Database\Factories;

use App\Models\Division;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Division> */
class DivisionFactory extends Factory
{
    protected $model = Division::class;

    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Finance', 'HRD', 'Sales', 'Marketing', 'Operasional', 'Produksi', 'Direksi', 'Umum', 'IT',
        ]);

        return [
            'name' => $name,
            'code' => strtoupper(substr($name, 0, 3)),
            'is_active' => true,
        ];
    }
}
