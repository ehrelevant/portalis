<?php

namespace Database\Seeders;

use App\Models\Faculty;
use App\Models\Supervisor;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        #Supervisor Seeder
        for ($x = 1; $x <= 15; $x++) {
            Supervisor::factory()->create([
                'company_id' => fake()->numberBetween(1, 10),
            ]);
            User::factory()->create([
                'email' => "supervisor$x@example.com",
                'role' => 'supervisor',
                'role_id' => $x,
                'first_name' => fake()->firstName(),
                'middle_name' => fake()->randomLetter(),
                'last_name' => fake()->lastName(),
                'year' => 2025,
            ]);
        }

        #Faculty Seeders
        Faculty::factory()->create([
            'section' => 'Truth',
        ]);
        User::factory()->create([
            'email' => 'faculty@example.com',
            'role' => 'faculty',
            'role_id' => 1,
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->randomLetter(),
            'last_name' => fake()->lastName(),
            'year' => 2025,
        ]);

        Faculty::factory()->create([
            'section' => 'Excellence',
        ]);
        User::factory()->create([
            'email' => 'faculty2@example.com',
            'role' => 'faculty',
            'role_id' => 2,
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->randomLetter(),
            'last_name' => fake()->lastName(),
            'year' => 2025,
        ]);

        #Admin Seeder
        User::factory()->create([
            'email' => 'admin@example.com',
            'role' => 'admin',
            'role_id' => 1,
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->randomLetter(),
            'last_name' => fake()->lastName(),
            'year' => 2025,
        ]);

        #Special User Seeds (for emailing purposes)
        Faculty::factory()->create([
            'section' => 'Service',
        ]);
        User::factory()->create([
            'email' => 'eacastillo1@up.edu.ph',
            'role' => 'faculty',
            'role_id' => 3,
            'first_name' => 'Ehren',
            'middle_name' => 'Middle',
            'last_name' => 'Castillo',
        ]);

        User::factory()->create([
            'email' => 'elsaavedra@up.edu.ph',
            'role' => 'admin',
            'role_id' => 2,
            'first_name' => 'Eoghaine Czeriel',
            'middle_name' => 'Middle',
            'last_name' => 'Saavedra',
        ]);

        Supervisor::factory()->create([
            'company_id' => 3,
        ]);
        User::factory()->create([
            'email' => 'drbermudez1@up.edu.ph',
            'role' => 'supervisor',
            'role_id' => 16,
            'first_name' => 'Dominic Lawrence',
            'middle_name' => 'Middle',
            'last_name' => 'Bermudez',
        ]);

        $this->call([
            DemoStudentSeeder::class,
        ]);
    }
}
