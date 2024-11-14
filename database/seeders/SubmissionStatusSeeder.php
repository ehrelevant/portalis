<?php

namespace Database\Seeders;

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
        SubmissionStatus::factory()->create([
            'student_number' => 202200000,
            'requirement_id' => 1,
            'status' => 'pending',
        ]);
        SubmissionStatus::factory()->create([
            'student_number' => 202200000,
            'requirement_id' => 2,
            'status' => 'pending',
        ]);
        SubmissionStatus::factory()->create([
            'student_number' => 202200000,
            'requirement_id' => 3,
            'status' => 'pending',
        ]);
        SubmissionStatus::factory()->create([
            'student_number' => 202200000,
            'requirement_id' => 4,
            'status' => 'pending',
        ]);
        SubmissionStatus::factory()->create([
            'student_number' => 202200000,
            'requirement_id' => 5,
            'status' => 'pending',
        ]);
        SubmissionStatus::factory()->create([
            'student_number' => 202200000,
            'requirement_id' => 6,
            'status' => 'pending',
        ]);
        SubmissionStatus::factory()->create([
            'student_number' => 202200000,
            'requirement_id' => 7,
            'status' => 'pending',
        ]);
    }
}
