<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class NotificationFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = Notification::class;

    /**
     * Definiere den Default-Zustand des Models.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        // Wähle einen zufälligen User als "notifiable"
        $user = User::inRandomOrder()->first() ?? User::factory()->create();

        return [
            'id' => (string) Str::uuid(),
            'type' => 'App\Notifications\TestNotification', 
            // oder z.B. 'App\Notifications\NewFollowerNotification'

            'notifiable_type' => $user->getMorphClass(), 
            // => typischerweise 'App\Models\User'

            'notifiable_id' => $user->id,

            // JSON-Datenfeld
            'data' => [
                'title' => $this->faker->sentence,
                'message' => $this->faker->paragraph,
                'link' => $this->faker->url,
            ],

            // Manchmal gelesen, manchmal nicht
            'read_at' => $this->faker->boolean(20)  // 20% Chance gelesen
                ? $this->faker->dateTimeBetween('-7 days', 'now')
                : null,

            'created_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'updated_at' => function (array $attributes) {
                return $attributes['read_at'] ?? $attributes['created_at'];
            },
        ];
    }
}
