<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Faculty;
use App\Models\Requirement;
use App\Models\Student;
use App\Models\Submission;
use App\Models\SubmissionStatus;
use App\Models\Supervisor;
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
        Company::factory()->create([
            'company_name' => 'Company Name',
        ]);
        Supervisor::factory()->create([
            'company_id' => 1,
        ]);
        Faculty::factory()->create([]);
        Student::factory()->create([
            'student_number' => 202200000,
            'supervisor_id' => 1,
            'faculty_id' => 1,
            'grade' => 69.21,
        ]);

        User::factory()->create([
            'email' => 'student@example.com',
            'role' => 'student',
            'role_id' => 202200000,
            'first_name' => 'first',
            'middle_name' => 'middle',
            'last_name' => 'last',
        ]);
        User::factory()->create([
            'email' => 'supervisor@example.com',
            'role' => 'supervisor',
            'role_id' => 1,
            'first_name' => 'first',
            'middle_name' => 'middle',
            'last_name' => 'last',
        ]);
        User::factory()->create([
            'email' => 'faculty@example.com',
            'role' => 'faculty',
            'role_id' => 1,
            'first_name' => 'first',
            'middle_name' => 'middle',
            'last_name' => 'last',
        ]);
        User::factory()->create([
            'email' => 'admin@example.com',
            'role' => 'admin',
            'role_id' => 1,
            'first_name' => 'first',
            'middle_name' => 'middle',
            'last_name' => 'last',
        ]);


        Student::factory()->create([
            'student_number' => 202200001,
            'supervisor_id' => 1,
            'faculty_id' => 1,
            'grade' => 69.21,
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
        ]);
        User::factory()->create([
            'email' => 'drbermudez1@up.edu.ph',
            'role' => 'student',
            'role_id' => 202200004,
            'first_name' => 'Dominic Lawrence',
            'middle_name' => 'Middle',
            'last_name' => 'Bermudez',
        ]);


        $this->call([
            RequirementSeeder::class,
            SubmissionStatusSeeder::class,
        ]);

        $this->call([
            RatingQuestionSeeder::class,
            OpenEndedQuestionSeeder::class,
        ]);

        WebsiteState::factory()->create([
            'phase' => 'pre',
        ]);
    }
}
