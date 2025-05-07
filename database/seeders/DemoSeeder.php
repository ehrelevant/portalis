<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\WebsiteState;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($x = 1; $x <= 10; $x++) {
            Company::factory()->create([
                'company_name' => "Company $x",
                'year' => 2025,
            ]);
        }

        #User Seeder
        $this->call([
            DemoUserSeeder::class,
        ]);
        
        #Requirement Seeder
        $this->call([
            RequirementSeeder::class,
        ]);

        #Form Seeder
        $this->call([
            FormSeeder::class,
        ]);

        #State Phase Seed (set to preintership)
        WebsiteState::factory()->create([
            'phase' => 'pre',
        ]);
    }
}
