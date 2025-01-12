<?php

namespace Database\Seeders;

use App\Models\Company;
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

        $this->call([
            UserSeeder::class,
        ]);

        $this->call([
            RequirementSeeder::class,
            StatusSeeder::class,
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
