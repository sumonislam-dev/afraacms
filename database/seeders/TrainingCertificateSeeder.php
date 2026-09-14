<?php

namespace Database\Seeders;

use App\CMS\Services\EnrollmentService;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Database\Seeder;

/**
 * Seeds a realistic Student -> Course -> Enrollment -> Certificate chain for
 * the training/certification module, issuing certificates through the real
 * EnrollmentService::issueCertificate() flow (not a factory shortcut) so
 * every certificate this creates is a genuine row in `certificates` with a
 * real certificate number, verification code, and working QR/verify link -
 * useful for demoing or manually testing /verify end to end.
 *
 * Safe to run repeatedly: like TestDataSeeder, it always creates a fresh
 * batch rather than matching existing rows, so re-running just adds more
 * sample data instead of erroring.
 *
 * Run standalone:
 *   php artisan db:seed --class=TrainingCertificateSeeder
 */
class TrainingCertificateSeeder extends Seeder
{
    private const SESSIONS = ['2023-2024', '2024-2025', '2025-2026'];

    private const STUDENTS_PER_SESSION = 6;

    public function run(EnrollmentService $enrollments): void
    {
        $courses = collect([
            ['course_name' => 'Electrical Installation and Maintenance Course', 'duration' => '02 Years'],
            ['course_name' => 'Computer Application and Office Management', 'duration' => '06 Months'],
            ['course_name' => 'Garments and Sewing Machine Operation', 'duration' => '06 Months'],
            ['course_name' => 'Mobile Servicing Course', 'duration' => '03 Months'],
        ])->map(fn (array $attributes) => Course::factory()->create($attributes));

        $issued = 0;
        $revoked = 0;
        $pending = 0;

        foreach (self::SESSIONS as $session) {
            $students = Student::factory()->count(self::STUDENTS_PER_SESSION)->create();

            foreach ($students as $index => $student) {
                $enrollment = Enrollment::factory()->passed()->create([
                    'student_id' => $student->id,
                    'course_id' => $courses->random()->id,
                    'session' => $session,
                ]);

                // Leave one enrollment per session not-yet-issued, so the
                // demo data covers every certificate_status: not_issued,
                // valid, and (one per session) revoked.
                if ($index === self::STUDENTS_PER_SESSION - 1) {
                    $pending++;

                    continue;
                }

                $enrollments->issueCertificate($enrollment);
                $issued++;

                if ($index === self::STUDENTS_PER_SESSION - 2) {
                    $enrollments->revokeCertificate($enrollment);
                    $revoked++;
                }
            }
        }

        $this->command?->info(
            "Training certificate data seeded: {$courses->count()} courses, "
            .(count(self::SESSIONS) * self::STUDENTS_PER_SESSION)." enrollments "
            ."({$issued} issued, {$revoked} of those revoked, {$pending} left pending)."
        );
    }
}
