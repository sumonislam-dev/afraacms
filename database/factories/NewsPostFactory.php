<?php

namespace Database\Factories;

use App\Models\NewsPost;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NewsPost>
 */
class NewsPostFactory extends Factory
{
    protected $model = NewsPost::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => ucfirst(fake()->unique()->words(4, true)),
            'slug' => fake()->unique()->slug(),
            'excerpt' => fake()->sentence(),
            'content' => fake()->paragraphs(3, true),
            'published_at' => fake()->dateTimeBetween('-6 months', 'now'),
            'is_featured' => false,
            'status' => 'draft',
        ];
    }

    /**
     * Indicate that the post is published.
     */
    public function published(): static
    {
        return $this->state(fn () => ['status' => 'published']);
    }
}
