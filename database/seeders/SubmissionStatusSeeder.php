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
        for($sn = 202200000; $sn <= 202200004; ++$sn) {
            for($id = 1; $id <= 7; ++$id) {
                SubmissionStatus::factory()->create([
                    'student_number' => $sn,
                    'requirement_id' => $id,
                    'status' => 'pending',
                ]);
            }
        }
    }
}
