<?php

namespace Database\Factories;

use App\Models\ProjectCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectCategory>
 */
class ProjectCategoryFactory extends Factory
{
    protected $model = ProjectCategory::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = ucfirst(fake()->unique()->word());

        return [
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name),
        ];
    }
}
