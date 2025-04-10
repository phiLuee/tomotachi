<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'content' => $this->faker->paragraph(rand(2, 6)),
            'parent_id' => null, // Standard: Haupt-Post
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }

    /**
     * State für Haupt-Posts:
     * - parent_id = null
     */
    public function mainPost()
    {
        return $this->state(function (array $attributes) {
            return [
                'parent_id' => null,
            ];
        });
    }

    /**
     * State für Kommentare:
     * - parent_id zeigt auf einen anderen Post
     */
    public function comment()
    {
        return $this->state(function (array $attributes) {
            // Sicherstellen, dass es schon einen Post gibt, auf den wir zeigen können.
            $existingPostId = Post::inRandomOrder()->first()?->id;

            return [
                'parent_id' => $existingPostId,
            ];
        });
    }
}
