<?php

namespace Database\Seeders;

use App\Models\CompanyEvaluationStatus;
use App\Models\InternEvaluationStatus;
use App\Models\Requirement;
use App\Models\Student;
use App\Models\SubmissionStatus;
use App\Models\ReportStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
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
                    'status' => 'unsubmitted',
                ]);
            }

            ReportStatus::factory()->create([
                'student_number' => $student->student_number,
                'supervisor_id' => $student->supervisor_id,
                'status' => 'unsubmitted',
            ]);

            InternEvaluationStatus::factory()->create([
                'student_number' => $student->student_number,
                'supervisor_id' => $student->supervisor_id,
                'status' => 'unsubmitted',
            ]);

            CompanyEvaluationStatus::factory()->create([
                'student_number' => $student->student_number,
                'company_id' => $student->supervisor()->company_id ?? 1,
                'status' => 'unsubmitted',
            ]);
        }
    }
}
