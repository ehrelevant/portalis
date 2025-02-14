<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
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
        User::factory()->create([
            'email' => 'portalis195@gmail.com',
            'role' => 'admin',
            'role_id' => 1,
            'first_name' => 'Portalis',
            'middle_name' => '',
            'last_name' => 'Admin',
        ]);

        $this->call([
            RequirementSeeder::class,
            FormSeeder::class,
        ]);

        WebsiteState::factory()->create([
            'phase' => 'pre',
        ]);
    }
}
