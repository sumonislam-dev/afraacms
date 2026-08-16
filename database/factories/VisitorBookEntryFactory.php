<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\VisitorBookEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VisitorBookEntry>
 */
class VisitorBookEntryFactory extends Factory
{
    protected $model = VisitorBookEntry::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'visitor_name' => fake()->name(),
            'visitor_email' => fake()->safeEmail(),
            'opinion' => fake()->paragraph(),
            'status' => 'pending',
            'ip_address' => fake()->ipv4(),
        ];
    }

    /**
     * Indicate that the entry has been approved.
     */
    public function approved(): static
    {
        return $this->state(fn () => ['status' => 'approved']);
    }

    /**
     * Indicate that the entry has been rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn () => ['status' => 'rejected']);
    }
}
