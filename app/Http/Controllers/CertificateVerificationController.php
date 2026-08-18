<?php

namespace App\Http\Controllers;

use App\CMS\Services\CertificateService;
use App\CMS\Services\EnrollmentService;
use Illuminate\View\View;

class CertificateVerificationController extends Controller
{
    public function __construct(
        private readonly CertificateService $certificates,
        private readonly EnrollmentService $enrollments,
    ) {}

    /**
     * Show the public certificate lookup form, and the result if a
     * certificate number or verification code was given (typed in, or
     * arriving via a scanned QR code's ?code= link).
     *
     * Checks the generic Certificate table first, then falls back to
     * Enrollment-issued certificates (the training/course certificate
     * system) - the two share this one public lookup page rather than
     * each getting their own.
     */
    public function index(): View
    {
        $identifier = trim((string) request('code', request('number', '')));

        $certificate = null;
        $enrollment = null;

        if ($identifier !== '') {
            $certificate = $this->certificates->findForVerification($identifier);

            if (! $certificate) {
                $enrollment = $this->enrollments->findForVerification($identifier);
            }
        }

        return view('frontend.verify', [
            'identifier' => $identifier,
            'certificate' => $certificate,
            'enrollment' => $enrollment,
        ]);
    }
}
