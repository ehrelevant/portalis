<?php

namespace Database\Seeders;

use App\Models\Requirement;
use App\Models\Student;
use App\Models\SubmissionStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RequirementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Requirement::factory()->create([
            'requirement_name' => 'Internship Agreement',
            'deadline' => null,
        ]);
        Requirement::factory()->create([
            'requirement_name' => 'Medical Certificate',
            'deadline' => null,
        ]);
        Requirement::factory()->create([
            'requirement_name' => 'Signed Work Plan',
            'deadline' => null,
        ]);
        Requirement::factory()->create([
            'requirement_name' => 'Student\'s ID',
        ]);
        Requirement::factory()->create([
            'requirement_name' => 'Supervisor\'s ID',
        ]);
        Requirement::factory()->create([
            'requirement_name' => 'Parent/Guardian\'s ID',
        ]);

        foreach (Student::all() as $student) {
            foreach (Requirement::all() as $requirement) {
                SubmissionStatus::factory()->create([
                    'student_number' => $student->student_number,
                    'requirement_id' => $requirement->id,
                    'status' => 'unsubmitted',
                ]);
            }
        }
    }
}
