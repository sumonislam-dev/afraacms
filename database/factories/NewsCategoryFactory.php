<?php

namespace Database\Factories;

use App\Models\NewsCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<NewsCategory>
 */
class NewsCategoryFactory extends Factory
{
    protected $model = NewsCategory::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = ucfirst(fake()->unique()->word());

        return [
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }
}
