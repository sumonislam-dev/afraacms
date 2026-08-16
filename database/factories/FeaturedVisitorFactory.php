<?php

namespace Database\Factories;

use App\Models\FeaturedVisitor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FeaturedVisitor>
 */
class FeaturedVisitorFactory extends Factory
{
    protected $model = FeaturedVisitor::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'organization' => fake()->company(),
            'country' => fake()->country(),
            'visited_at' => fake()->dateTimeBetween('-2 years', 'now'),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
