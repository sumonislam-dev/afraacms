<?php

namespace Database\Factories;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Enrollment>
 */
class EnrollmentFactory extends Factory
{
    protected $model = Enrollment::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'course_id' => Course::factory(),
            'session' => fake()->randomElement(['2023-2024', '2024-2025', '2025-2026']),
            // Not fake()->unique(): that only tracks uniqueness within the
            // current process and would collide with a prior seeder run's
            // rows (same lesson as TestDataSeeder::uniqueSlug()).
            'roll_number' => fake()->numberBetween(1, 9999).'-'.Str::random(4),
            'registration_number' => fake()->numberBetween(10000, 99999).'-'.Str::random(4),
            'admission_date' => fake()->dateTimeBetween('-2 years', '-1 year'),
            'completion_date' => null,
            'grade' => null,
            'grade_point' => null,
            'grade_scale' => null,
            'result_status' => 'pending',
        ];
    }

    /**
     * Indicate that the student has passed with a grade recorded.
     */
    public function passed(): static
    {
        return $this->state(fn () => [
            'completion_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'grade' => fake()->randomElement(['A+', 'A', 'A-', 'B+', 'B']),
            'grade_point' => fake()->randomFloat(2, 2, 4),
            'grade_scale' => 4.00,
            'result_status' => 'passed',
        ]);
    }

    /**
     * Indicate that the student failed the course.
     */
    public function failed(): static
    {
        return $this->state(fn () => [
            'completion_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'grade' => 'F',
            'grade_point' => fake()->randomFloat(2, 0, 1.99),
            'grade_scale' => 4.00,
            'result_status' => 'failed',
        ]);
    }

    /**
     * Indicate that a certificate has been issued for this (passed)
     * enrollment: creates the linked Certificate row afterCreating() rather
     * than as a plain state(), since certificate_status is now computed
     * from that relation instead of being a column on this model.
     */
    public function certificateIssued(): static
    {
        return $this->passed()->afterCreating(fn (Enrollment $enrollment) => Certificate::factory()->create([
            'enrollment_id' => $enrollment->id,
            'recipient_name' => $enrollment->student->name,
            'program' => $enrollment->course->course_name,
            'issued_at' => $enrollment->completion_date,
            'status' => 'valid',
        ]));
    }

    /**
     * Indicate that this enrollment's certificate has been revoked.
     */
    public function certificateRevoked(): static
    {
        return $this->passed()->afterCreating(fn (Enrollment $enrollment) => Certificate::factory()->create([
            'enrollment_id' => $enrollment->id,
            'recipient_name' => $enrollment->student->name,
            'program' => $enrollment->course->course_name,
            'issued_at' => $enrollment->completion_date,
            'status' => 'revoked',
        ]));
    }
}
