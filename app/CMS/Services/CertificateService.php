<?php

namespace App\CMS\Services;

use App\Models\Certificate;

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
            ->where('certificate_number', $identifier)
            ->orWhere('verification_code', $identifier)
            ->first();
    }

    /**
     * Create a new certificate.
     */
    public function create(array $data): Certificate
    {
        return Certificate::create($data);
    }

    /**
     * Update an existing certificate.
     */
    public function update(Certificate $certificate, array $data): Certificate
    {
        $certificate->update($data);

        return $certificate;
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
