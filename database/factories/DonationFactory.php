<?php

namespace Database\Factories;

use App\Models\Donation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Donation>
 */
class DonationFactory extends Factory
{
    protected $model = Donation::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'donor_name' => fake()->name(),
            'donor_email' => fake()->safeEmail(),
            'amount' => fake()->randomFloat(2, 5, 5000),
            'currency' => config('donations.default_currency', 'BDT'),
            'method' => fake()->randomElement(array_keys(config('donations.methods'))),
            'donated_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'status' => 'completed',
            'notes' => null,
        ];
    }

    /**
     * Indicate that the donation has been refunded.
     */
    public function refunded(): static
    {
        return $this->state(fn () => ['status' => 'refunded']);
    }

    /**
     * Indicate that no donor email was recorded.
     */
    public function withoutEmail(): static
    {
        return $this->state(fn () => ['donor_email' => null]);
    }
}
