<?php

namespace App\Http\Controllers;

use App\CMS\Services\CertificateService;
use Illuminate\View\View;

class CertificateVerificationController extends Controller
{
    public function __construct(private readonly CertificateService $certificates)
    {
    }

    /**
     * Show the public certificate lookup form, and the result if a
     * certificate number or verification code was given (typed in, or
     * arriving via a scanned QR code's ?code= link).
     */
    public function index(): View
    {
        $identifier = trim((string) request('code', request('number', '')));

        $certificate = $identifier !== '' ? $this->certificates->findForVerification($identifier) : null;

        return view('frontend.verify', [
            'identifier' => $identifier,
            'certificate' => $certificate,
        ]);
    }
}
