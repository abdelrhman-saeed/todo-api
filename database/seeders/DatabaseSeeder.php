<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(['email' => 'user@example.com', 'role' => 'user'])->create();
        User::factory(['email' => 'manager@example.com', 'role' => 'manager'])->create();

        User::factory(10)->create(['role' => 'user']);
        User::factory(10)->create(['role' => 'manager']);

        Task::factory(40)->create();
    }
}
