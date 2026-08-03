<?php

namespace Database\Factories;

use App\Models\Banner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Banner>
 */
class BannerFactory extends Factory
{
    protected $model = Banner::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => 'homepage',
            'title' => ucfirst(fake()->words(3, true)),
            'subtitle' => fake()->sentence(),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
