<?php

namespace App\CMS\Services;

use App\Models\Certificate;
use App\Models\Enrollment;

/**
 * Deliberately does NOT use the CachesForFrontend/rememberForever pattern
 * the other content services use: a revoked certificate must stop verifying
 * as valid immediately, not after a cache TTL, so every lookup here hits the
 * database directly.
 */
class CertificateService
{
    /**
     * Find a certificate by its public certificate number or its
     * QR/verification code - either is a valid lookup key on the public
     * verify page.
     */
    public function findForVerification(string $identifier): ?Certificate
    {
        return Certificate::query()
            ->with(['enrollment.course', 'project'])
            ->where('certificate_number', $identifier)
            ->orWhere('verification_code', $identifier)
            ->first();
    }

    /**
     * Create a new certificate.
     */
    public function create(array $data): Certificate
    {
        return Certificate::create($this->withEnrollmentRecipient($data));
    }

    /**
     * Update an existing certificate.
     */
    public function update(Certificate $certificate, array $data): Certificate
    {
        $certificate->update($this->withEnrollmentRecipient($data));

        return $certificate;
    }

    /**
     * When an enrollment is picked, the recipient name/program are always
     * derived from its Student/Course - never trusted from the request -
     * so the form's "From Enrollment" fields can stay disabled client-side
     * and the two never drift apart.
     */
    private function withEnrollmentRecipient(array $data): array
    {
        if (empty($data['enrollment_id'])) {
            return $data;
        }

        $enrollment = Enrollment::with(['student', 'course'])->findOrFail($data['enrollment_id']);

        $data['recipient_name'] = $enrollment->student->name;
        $data['program'] = $enrollment->course->course_name;

        return $data;
    }

    /**
     * Delete a certificate.
     */
    public function delete(Certificate $certificate): void
    {
        $certificate->delete();
    }

    /**
     * Restore a soft-deleted certificate.
     */
    public function restore(Certificate $certificate): Certificate
    {
        $certificate->restore();

        return $certificate;
    }

    /**
     * Permanently delete a soft-deleted certificate.
     */
    public function forceDelete(Certificate $certificate): void
    {
        $certificate->forceDelete();
    }
}
