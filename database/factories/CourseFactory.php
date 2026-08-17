<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_name' => fake()->randomElement([
                'Electrical Installation and Maintenance Course',
                'Computer Application and Office Management',
                'Garments and Sewing Machine Operation',
                'Mobile Servicing Course',
            ]),
            'duration' => fake()->randomElement(['06 months', '01 Year', '02 Years']),
            'description' => fake()->paragraph(),
            'status' => 'active',
        ];
    }

    /**
     * Indicate that the course is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn () => ['status' => 'inactive']);
    }
}
