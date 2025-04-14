<?php

namespace Database\Seeders;

use App\Models\Faculty;
use App\Models\Supervisor;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Supervisor::factory()->create([
            'company_id' => 1,
        ]);
        User::factory()->create([
            'email' => 'supervisor@example.com',
            'role' => 'supervisor',
            'role_id' => 1,
            'first_name' => 'First',
            'middle_name' => 'Middle',
            'last_name' => 'Supervisor',
            'year' => 2025,
        ]);

        Supervisor::factory()->create([
            'company_id' => 2,
        ]);
        User::factory()->create([
            'email' => 'supervisor2@example.com',
            'role' => 'supervisor',
            'role_id' => 2,
            'first_name' => 'Second',
            'middle_name' => 'Middle',
            'last_name' => 'Supervisor',
            'year' => 2025,
        ]);

        Faculty::factory()->create([
            'section' => 'U1',
        ]);
        User::factory()->create([
            'email' => 'faculty@example.com',
            'role' => 'faculty',
            'role_id' => 1,
            'first_name' => 'First',
            'middle_name' => 'Middle',
            'last_name' => 'Faculty',
            'year' => 2025,
        ]);

        Faculty::factory()->create([
            'section' => 'U2',
        ]);
        User::factory()->create([
            'email' => 'faculty2@example.com',
            'role' => 'faculty',
            'role_id' => 2,
            'first_name' => 'Second',
            'middle_name' => 'Middle',
            'last_name' => 'Faculty',
            'year' => 2025,
        ]);

        User::factory()->create([
            'email' => 'admin@example.com',
            'role' => 'admin',
            'role_id' => 1,
            'first_name' => 'First',
            'middle_name' => 'Middle',
            'last_name' => 'Last',
            'year' => 2025,
        ]);

        $this->call([
            TestStudentSeeder::class,
        ]);
    }
}
