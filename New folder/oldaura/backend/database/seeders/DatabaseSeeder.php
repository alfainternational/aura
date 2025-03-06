<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\UsersTableSeeder;
use Database\Seeders\SampleUsersSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call the SampleUsersSeeder to reset and create sample users
        $this->call(SampleUsersSeeder::class);
    }
}
