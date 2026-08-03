<?php

namespace Database\Factories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Page>
 */
class PageFactory extends Factory
{
    protected $model = Page::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => ucfirst(fake()->unique()->words(3, true)),
            'slug' => fake()->unique()->slug(),
            'status' => 'draft',
            'template' => 'default',
            'content' => fake()->paragraph(),
            'published_at' => null,
        ];
    }

    /**
     * Indicate that the page is published and already visible.
     */
    public function published(): static
    {
        return $this->state(fn () => [
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);
    }
}
