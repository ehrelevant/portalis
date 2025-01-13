<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Student::factory()->create([
            'student_number' => 202200000,
            'supervisor_id' => 1,
            'faculty_id' => 1,
            'grade' => 69.21,
            'phase' => 'pre',
            'wordpress_name' => 'student',
            'wordpress_email' => 'student@example.com',
        ]);
        User::factory()->create([
            'email' => 'student@example.com',
            'role' => 'student',
            'role_id' => 202200000,
            'first_name' => 'First',
            'middle_name' => 'Middle',
            'last_name' => 'Last',
        ]);

        Student::factory()->create([
            'student_number' => 202200001,
            'supervisor_id' => 1,
            'faculty_id' => 1,
            'grade' => 69.21,
            'phase' => 'pre',
            'wordpress_name' => 'ehren',
            'wordpress_email' => 'eacastillo1@up.edu.ph',
        ]);
        User::factory()->create([
            'email' => 'eacastillo1@up.edu.ph',
            'role' => 'student',
            'role_id' => 202200001,
            'first_name' => 'Ehren',
            'middle_name' => 'Middle',
            'last_name' => 'Castillo',
        ]);

        Student::factory()->create([
            'student_number' => 202200002,
            'supervisor_id' => 1,
            'faculty_id' => 1,
            'grade' => 69.21,
            'phase' => 'pre',
            'wordpress_name' => 'edzo',
            'wordpress_email' => 'csacyatan1@up.edu.ph',
        ]);
        User::factory()->create([
            'email' => 'csacyatan1@up.edu.ph',
            'role' => 'student',
            'role_id' => 202200002,
            'first_name' => 'Clyde Ambroz',
            'middle_name' => 'Middle',
            'last_name' => 'Acyatan',
        ]);

        Student::factory()->create([
            'student_number' => 202200003,
            'supervisor_id' => 1,
            'faculty_id' => 1,
            'grade' => 69.21,
            'phase' => 'pre',
            'wordpress_name' => 'yuwen',
            'wordpress_email' => 'elsaavedra@up.edu.ph',
        ]);
        User::factory()->create([
            'email' => 'elsaavedra@up.edu.ph',
            'role' => 'student',
            'role_id' => 202200003,
            'first_name' => 'Eoghaine Czeriel',
            'middle_name' => 'Middle',
            'last_name' => 'Saavedra',
        ]);

        Student::factory()->create([
            'student_number' => 202200004,
            'supervisor_id' => 1,
            'faculty_id' => 1,
            'grade' => 69.21,
            'phase' => 'pre',
            'wordpress_name' => 'lawrence',
            'wordpress_email' => 'drbermudez1@up.edu.ph',
        ]);
        User::factory()->create([
            'email' => 'drbermudez1@up.edu.ph',
            'role' => 'student',
            'role_id' => 202200004,
            'first_name' => 'Dominic Lawrence',
            'middle_name' => 'Middle',
            'last_name' => 'Bermudez',
        ]);
    }
}
