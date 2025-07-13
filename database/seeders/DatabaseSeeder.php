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
        User::factory(3)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $user = User::create([
            'name' => 'John Doe',
            'email' => 'Djohn@test.com',
            'password' => bcrypt('password123'),
        ]);

        // Create tasks for the user
        Task::create([
            'user_id' => $user->id,
            'title' => 'Apprendre Laravel',
            'description' => 'Suivre le tutoriel complet',
            'priority' => 2,
            'is_completed' => false,
        ]);

        Task::create([
            'user_id' => $user->id,
            'title' => 'Configurer PostgreSQL',
            'description' => 'Installer et configurer la base de données',
            'priority' => 1,
            'is_completed' => true,
        ]);

        Task::create([
            'user_id' => $user->id,
            'title' => 'Intégrer Vue.js',
            'description' => 'Créer les composants frontend',
            'priority' => 0,
            'is_completed' => false,
            'due_date' => now()->addDays(7),
        ]);


    }
}
