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
    public function __construct(private readonly CertificateService $certificates) {}

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
     * Issue the certificate for a passed enrollment: creates (or reactivates)
     * the linked Certificate row - certificate_status/certificate_number/
     * verification_code are computed straight from it, so there's nothing
     * else to update. Refuses if a certificate was already issued (has its
     * own explicit revoke() instead) or the student hasn't passed yet.
     */
    public function issueCertificate(Enrollment $enrollment): bool
    {
        if ($enrollment->certificate_status !== 'not_issued' || $enrollment->result_status !== 'passed') {
            return false;
        }

        $certificate = $enrollment->certificate ?? $this->certificates->create([
            'enrollment_id' => $enrollment->id,
            'issued_at' => $enrollment->completion_date ?? now(),
            'status' => 'valid',
        ]);

        if ($certificate->status !== 'valid') {
            $certificate->update(['status' => 'valid']);
        }

        $enrollment->setRelation('certificate', $certificate);

        return true;
    }

    /**
     * Revoke an already-issued certificate - it immediately stops verifying
     * as valid, but keeps its certificate_number/verification_code so the
     * printed document still resolves (to a "revoked" result) rather than a
     * bare "not found". certificate_status === 'valid' can only be true when
     * a linked Certificate exists (it's computed from one), so there's no
     * null case to guard here.
     */
    public function revokeCertificate(Enrollment $enrollment): bool
    {
        if ($enrollment->certificate_status !== 'valid') {
            return false;
        }

        $enrollment->certificate->update(['status' => 'revoked']);

        return true;
    }
}
