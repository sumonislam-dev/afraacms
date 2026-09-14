<?php

namespace Database\Factories;

use App\Models\StoryCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<StoryCategory>
 */
class StoryCategoryFactory extends Factory
{
    protected $model = StoryCategory::class;

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
