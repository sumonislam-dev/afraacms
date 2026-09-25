<?php

namespace Database\Factories;

use App\Models\AnnualReport;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AnnualReport>
 */
class AnnualReportFactory extends Factory
{
    protected $model = AnnualReport::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $year = fake()->numberBetween(2015, 2025);

        return [
            'title' => "Annual Report {$year}-".($year + 1),
            'year' => "{$year}-".($year + 1),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
