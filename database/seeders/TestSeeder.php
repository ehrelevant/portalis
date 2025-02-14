<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\WebsiteState;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Company::factory()->create([
            'company_name' => 'Company 1',
        ]);
        Company::factory()->create([
            'company_name' => 'Company 2',
        ]);

        $this->call([
            TestUserSeeder::class,
        ]);

        $this->call([
            RequirementSeeder::class,
        ]);

        $this->call([
            FormSeeder::class,
        ]);

        WebsiteState::factory()->create([
            'phase' => 'pre',
        ]);
    }
}
