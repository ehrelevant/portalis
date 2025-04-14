<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Faculty;
use App\Models\Supervisor;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
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
            'year' => 2025,
        ]);
    }
}
