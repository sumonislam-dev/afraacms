<?php

namespace App\CMS\Services;

use App\Models\Enrollment;

/**
 * Deliberately does NOT use the CachesForFrontend/rememberForever pattern
 * other content services use: a revoked certificate must stop verifying as
 * valid immediately, not after a cache TTL, so every lookup here hits the
 * database directly (same reasoning as CertificateService).
 */
class EnrollmentService
{
    /**
     * Find an issued enrollment/certificate by its public certificate number
     * or its QR/verification code - either is a valid lookup key on the
     * public verify page. Enrollments that never had a certificate issued
     * have both fields null, so they can never match here.
     */
    public function findForVerification(string $identifier): ?Enrollment
    {
        return Enrollment::query()
            ->with(['student', 'course'])
            ->where('certificate_number', $identifier)
            ->orWhere('verification_code', $identifier)
            ->first();
    }

    /**
     * Create a new enrollment.
     */
    public function create(array $data): Enrollment
    {
        return Enrollment::create($data);
    }

    /**
     * Update an existing enrollment.
     */
    public function update(Enrollment $enrollment, array $data): Enrollment
    {
        $enrollment->update($data);

        return $enrollment;
    }

    /**
     * Delete an enrollment.
     */
    public function delete(Enrollment $enrollment): void
    {
        $enrollment->delete();
    }

    /**
     * Restore a soft-deleted enrollment.
     */
    public function restore(Enrollment $enrollment): Enrollment
    {
        $enrollment->restore();

        return $enrollment;
    }

    /**
     * Permanently delete a soft-deleted enrollment.
     */
    public function forceDelete(Enrollment $enrollment): void
    {
        $enrollment->forceDelete();
    }

    /**
     * Issue the certificate for a passed enrollment: marks it valid, which
     * triggers Enrollment's saving() hook to generate its certificate_number
     * and verification_code. Refuses if a certificate was already issued
     * (has its own explicit revoke() instead) or the student hasn't passed yet.
     */
    public function issueCertificate(Enrollment $enrollment): bool
    {
        if ($enrollment->certificate_status !== 'not_issued' || $enrollment->result_status !== 'passed') {
            return false;
        }

        $enrollment->forceFill(['certificate_status' => 'valid'])->save();

        return true;
    }

    /**
     * Revoke an already-issued certificate - it immediately stops verifying
     * as valid, but keeps its certificate_number/verification_code so the
     * printed document still resolves (to a "revoked" result) rather than
     * a bare "not found".
     */
    public function revokeCertificate(Enrollment $enrollment): bool
    {
        if ($enrollment->certificate_status !== 'valid') {
            return false;
        }

        $enrollment->forceFill(['certificate_status' => 'revoked'])->save();

        return true;
    }
}
