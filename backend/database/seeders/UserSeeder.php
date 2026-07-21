<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Division;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $it = Division::where('name', 'IT')->first();
        $sales = Division::where('name', 'Sales')->first();
        $hrd = Division::where('name', 'HRD')->first();

        // Akun demo — password semua: "password"
        User::firstOrCreate(
            ['email' => 'admin@gawe-qi.test'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => Role::Admin,
                'division_id' => $it?->id,
                'job_title' => 'Kepala IT',
                'is_active' => true,
            ],
        );

        User::firstOrCreate(
            ['email' => 'rifqi@gawe-qi.test'],
            [
                'name' => 'Rifqi Setiawan',
                'password' => Hash::make('password'),
                'role' => Role::ItSupport,
                'division_id' => $it?->id,
                'job_title' => 'IT Staff — All Role Support',
                'is_active' => true,
            ],
        );

        User::firstOrCreate(
            ['email' => 'agus@gawe-qi.test'],
            [
                'name' => 'Agus Pratama',
                'password' => Hash::make('password'),
                'role' => Role::ItSupport,
                'division_id' => $it?->id,
                'job_title' => 'IT Support',
                'is_active' => true,
            ],
        );

        User::firstOrCreate(
            ['email' => 'bagus@gawe-qi.test'],
            [
                'name' => 'Bagus Wicaksono',
                'password' => Hash::make('password'),
                'role' => Role::Client,
                'division_id' => $sales?->id,
                'job_title' => 'Sales Executive',
                'is_active' => true,
            ],
        );

        User::firstOrCreate(
            ['email' => 'dewi@gawe-qi.test'],
            [
                'name' => 'Dewi Lestari',
                'password' => Hash::make('password'),
                'role' => Role::Client,
                'division_id' => $hrd?->id,
                'job_title' => 'HR Officer',
                'is_active' => true,
            ],
        );
    }
}
