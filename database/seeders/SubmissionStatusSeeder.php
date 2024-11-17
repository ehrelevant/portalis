<?php

namespace Database\Seeders;

use App\Models\Requirement;
use App\Models\Student;
use App\Models\SubmissionStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubmissionStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Student::all() as $student) {
            foreach (Requirement::all() as $requirement) {
                SubmissionStatus::factory()->create([
                    'student_number' => $student->student_number,
                    'requirement_id' => $requirement->id,
                    'status' => 'pending',
                ]);
            }
        }
    }
}
