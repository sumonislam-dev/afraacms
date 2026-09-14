<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Creates a real `certificates` row for every enrollment whose certificate
 * was already issued (or revoked) the old way, before Certificate/Enrollment
 * were linked - so EnrollmentService::issueCertificate()/revokeCertificate()
 * (which now operate on the linked Certificate) have one to find, and so
 * the "Certificates" admin screen lists every certificate that actually
 * exists, not just ones issued after this migration.
 *
 * Preserves the enrollment's existing certificate_number/verification_code
 * wherever possible - those may already be printed on paper or shared as a
 * verify link - only regenerating certificate_number on the rare collision
 * with a pre-existing standalone Certificate row (both tables generated
 * numbers independently, from CERT-<year>-00001, before this migration).
 */
return new class extends Migration
{
    public function up(): void
    {
        $enrollments = DB::table('enrollments')
            ->whereIn('certificate_status', ['valid', 'revoked'])
            ->get();

        foreach ($enrollments as $enrollment) {
            if (DB::table('certificates')->where('enrollment_id', $enrollment->id)->exists()) {
                continue;
            }

            $student = DB::table('students')->where('id', $enrollment->student_id)->first();
            $course = DB::table('courses')->where('id', $enrollment->course_id)->first();
            $issuedAt = $enrollment->completion_date ?? $enrollment->created_at;

            DB::table('certificates')->insert([
                'enrollment_id' => $enrollment->id,
                'certificate_number' => $this->uniqueCertificateNumber(
                    $enrollment->certificate_number ?: null,
                    $issuedAt ? Carbon::parse($issuedAt)->year : now()->year,
                ),
                'verification_code' => $this->uniqueVerificationCode($enrollment->verification_code ?: null),
                'recipient_name' => $student->name ?? 'Unknown',
                'program' => $course->course_name ?? null,
                'issued_at' => $issuedAt ?? now(),
                'status' => $enrollment->certificate_status,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Best-effort only: distinguishing a backfilled row from one an admin
     * has since created/edited by hand isn't possible from the data alone,
     * so reversing this migration would risk deleting real certificates.
     */
    public function down(): void {}

    private function uniqueCertificateNumber(?string $preferred, int $year): string
    {
        if ($preferred && ! DB::table('certificates')->where('certificate_number', $preferred)->exists()) {
            return $preferred;
        }

        $sequence = DB::table('certificates')->whereYear('issued_at', $year)->count() + 1;
        $number = sprintf('CERT-%d-%05d', $year, $sequence);

        while (DB::table('certificates')->where('certificate_number', $number)->exists()) {
            $sequence++;
            $number = sprintf('CERT-%d-%05d', $year, $sequence);
        }

        return $number;
    }

    private function uniqueVerificationCode(?string $preferred): string
    {
        if ($preferred && ! DB::table('certificates')->where('verification_code', $preferred)->exists()) {
            return $preferred;
        }

        do {
            $code = Str::random(32);
        } while (DB::table('certificates')->where('verification_code', $code)->exists());

        return $code;
    }
};
