<?php

namespace Database\Factories;

use App\Models\Gallery;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Gallery>
 */
class GalleryFactory extends Factory
{
    protected $model = Gallery::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => ucfirst(fake()->unique()->words(2, true)),
            'slug' => fake()->unique()->slug(),
            'description' => fake()->sentence(),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
