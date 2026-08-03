<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => ucfirst(fake()->unique()->words(3, true)),
            'slug' => fake()->unique()->slug(),
            'excerpt' => fake()->sentence(),
            'content' => fake()->paragraph(),
            'is_featured' => false,
            'status' => 'draft',
        ];
    }

    /**
     * Indicate that the project is published.
     */
    public function published(): static
    {
        return $this->state(fn () => ['status' => 'published']);
    }
}
