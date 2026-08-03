<?php

namespace Database\Factories;

use App\Models\Page;
use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Section>
 */
class SectionFactory extends Factory
{
    protected $model = Section::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'page_id' => Page::factory(),
            'type' => 'hero',
            'heading' => ucfirst(fake()->words(3, true)),
            'subheading' => fake()->sentence(),
            'body' => fake()->paragraph(),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
