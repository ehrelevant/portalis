<?php

namespace Database\Seeders;

use App\Models\Requirement;
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
        ]);
        Requirement::factory()->create([
            'requirement_name' => 'Medical Certificate',
        ]);
        Requirement::factory()->create([
            'requirement_name' => 'Signed Work Plan',
        ]);
        Requirement::factory()->create([
            'requirement_name' => 'Student\'s Id',
        ]);
        Requirement::factory()->create([
            'requirement_name' => 'Faculty Adviser\'s ID',
        ]);
        Requirement::factory()->create([
            'requirement_name' => 'Supervisor\'s ID',
        ]);
        Requirement::factory()->create([
            'requirement_name' => 'Parent/Guardian\'s ID',
        ]);
    }
}
