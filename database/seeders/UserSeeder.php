<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // Wichtig für Passwort-Hashing
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Einen bekannten Test-User erstellen (optional, aber praktisch)
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            // Das Passwort 'password' wird durch die UserFactory gehasht
        ]);

        // 2. Mehrere zufällige User erstellen
        // Erstellt 10 zusätzliche zufällige User über die Factory
        User::factory()->count(10)->create();

        // Info-Ausgabe (optional)
        $this->command->info('UserSeeder executed: Created test user and 10 random users.');
    }
}