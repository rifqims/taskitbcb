<?php

namespace Database\Seeders;

use App\Enums\Priority;
use App\Models\SlaPolicy;
use Illuminate\Database\Seeder;

class SlaPolicySeeder extends Seeder
{
    public function run(): void
    {
        foreach (Priority::cases() as $priority) {
            SlaPolicy::firstOrCreate(
                ['priority' => $priority->value],
                ['resolution_minutes' => $priority->defaultSlaMinutes()],
            );
        }
    }
}
