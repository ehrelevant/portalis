<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\WebsiteState;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'email' => 'student@example.com',
            'role' => 'student',
        ]);
        User::factory()->create([
            'email' => 'supervisor@example.com',
            'role' => 'supervisor',
        ]);
        User::factory()->create([
            'email' => 'faculty@example.com',
            'role' => 'faculty',
        ]);
        User::factory()->create([
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        WebsiteState::factory()->create([
            'phase' => 'pre'
        ]);
    }
}
