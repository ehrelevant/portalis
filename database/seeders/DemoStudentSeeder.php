<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoStudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        #Student Seeder
        for ($x = 1; $x <= 30; $x++) {
            $fake_name = fake()->firstName();
            Student::factory()->create([
                'student_number' => fake()->unique()->numerify('2022#####'),
                'supervisor_id' => fake()->numberBetween(1, 15),
                'faculty_id' => null,
                'grade' => null,
                'wordpress_name' => $fake_name,
                'wordpress_email' => "student$x@example.com",
                'has_dropped' => false,
            ]);
            User::factory()->create([
                'email' => "student$x@example.com",
                'role' => 'student',
                'role_id' => $x,
                'first_name' => $fake_name,
                'middle_name' => fake()->randomLetter(),
                'last_name' => fake()->lastName(),
            ]);
        }

        Student::factory()->create([
            'student_number' => '203300001',
            'supervisor_id' => 2,
            'faculty_id' => null,
            'grade' => null,
            'wordpress_name' => 'edzo',
            'wordpress_email' => 'csacyatan1@up.edu.ph',
            'has_dropped' => false,
        ]);
        User::factory()->create([
            'email' => 'csacyatan1@up.edu.ph',
            'role' => 'student',
            'role_id' => 31,
            'first_name' => 'Clyde Ambroz',
            'middle_name' => 'Middle',
            'last_name' => 'Acyatan',
        ]);
    }
}
