<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User; // Import User Model
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Wähle eine zufällige, existierende User-ID aus.
            // Stellt sicher, dass UserSeeder vorher gelaufen ist!
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(), // Finde einen User oder erstelle einen neuen, falls keiner existiert (Fallback)
            'content' => $this->faker->paragraph(rand(2, 6)), // Erzeuge 2 bis 6 Absätze Text
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'), // Erstelle Posts innerhalb des letzten Jahres
            'updated_at' => function (array $attributes) {
                // updated_at sollte gleich oder nach created_at sein
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }
}