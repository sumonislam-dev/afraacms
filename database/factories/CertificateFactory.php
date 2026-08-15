<?php

namespace Database\Factories;

use App\Models\Certificate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Certificate>
 */
class CertificateFactory extends Factory
{
    protected $model = Certificate::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'recipient_name' => fake()->name(),
            'program' => fake()->randomElement(['Web Development Training', 'Vocational Skills Program', 'Scholarship Program']),
            'issued_at' => fake()->dateTimeBetween('-2 years', 'now'),
            'status' => 'valid',
            'notes' => null,
        ];
    }

    /**
     * Indicate that the certificate has been revoked.
     */
    public function revoked(): static
    {
        return $this->state(fn () => ['status' => 'revoked']);
    }
}
