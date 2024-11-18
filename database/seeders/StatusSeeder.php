<?php

namespace Database\Seeders;

use App\Models\CompanyEvaluationStatus;
use App\Models\InternEvaluationStatus;
use App\Models\Requirement;
use App\Models\Student;
use App\Models\SubmissionStatus;
use App\Models\WeeklyReportStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use PHPUnit\Metadata\Api\Requirements;

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
                    'status' => 'pending',
                ]);
            }

            for ($i = 1; $i <= 5; ++$i) {
                WeeklyReportStatus::factory()->create([
                    'student_number' => $student->student_number,
                    'supervisor_id' => $student->supervisor_id,
                    'week' => $i,
                    'status' => 'pending',
                ]);
            }

            InternEvaluationStatus::factory()->create([
                'student_number' => $student->student_number,
                'supervisor_id' => $student->supervisor_id,
                'status' => 'pending',
            ]);

            CompanyEvaluationStatus::factory()->create([
                'student_number' => $student->student_number,
                'company_id' => $student->supervisor()->company_id ?? 1,
                'status' => 'pending',
            ]);
        }
    }
}
