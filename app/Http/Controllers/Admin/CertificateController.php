<?php

namespace App\Http\Controllers\Admin;

use App\CMS\Services\CertificateService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCertificateRequest;
use App\Http\Requests\Admin\UpdateCertificateRequest;
use App\Models\Certificate;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class CertificateController extends Controller
{
    public function __construct(private readonly CertificateService $certificates)
    {
        $this->authorizeResource(Certificate::class, 'certificate');
    }

    /**
     * Display a listing of the certificates.
     */
    public function index(): View
    {
        $certificates = Certificate::with('project')
            ->when(request('search'), fn ($query, $search) => $query
                ->where('recipient_name', 'like', "%{$search}%")
                ->orWhere('certificate_number', 'like', "%{$search}%"))
            ->latest('issued_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.certificates.index', compact('certificates'));
    }

    /**
     * Display the given certificate, including its verification QR code.
     */
    public function show(Certificate $certificate): View
    {
        $certificate->loadMissing('project');

        return view('admin.certificates.show', compact('certificate'));
    }

    /**
     * Show the form for creating a new certificate.
     */
    public function create(): View
    {
        return view('admin.certificates.create');
    }

    /**
     * Store a newly created certificate.
     */
    public function store(StoreCertificateRequest $request): RedirectResponse
    {
        $this->certificates->create($request->validated());

        return redirect()->route('admin.certificates.index')->with('success', __('Certificate issued successfully.'));
    }

    /**
     * Show the form for editing the given certificate.
     */
    public function edit(Certificate $certificate): View
    {
        return view('admin.certificates.edit', compact('certificate'));
    }

    /**
     * Update the given certificate.
     */
    public function update(UpdateCertificateRequest $request, Certificate $certificate): RedirectResponse
    {
        $this->certificates->update($certificate, $request->validated());

        return redirect()->route('admin.certificates.index')->with('success', __('Certificate updated successfully.'));
    }

    /**
     * Delete the given certificate.
     */
    public function destroy(Certificate $certificate): RedirectResponse
    {
        $this->certificates->delete($certificate);

        return redirect()->route('admin.certificates.index')->with('success', __('Certificate deleted successfully.'));
    }

    /**
     * Display the trashed (soft-deleted) certificates.
     */
    public function trash(): View
    {
        $this->authorize('viewAny', Certificate::class);

        $certificates = Certificate::onlyTrashed()
            ->with('project')
            ->when(request('search'), fn ($query, $search) => $query->where('recipient_name', 'like', "%{$search}%"))
            ->orderByDesc('deleted_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.certificates.trash', compact('certificates'));
    }

    /**
     * Restore a trashed certificate.
     */
    public function restore(Certificate $certificate): RedirectResponse
    {
        $this->authorize('restore', $certificate);

        $this->certificates->restore($certificate);

        return redirect()->route('admin.certificates.trash')->with('success', __('Certificate restored successfully.'));
    }

    /**
     * Permanently delete a trashed certificate.
     */
    public function forceDelete(Certificate $certificate): RedirectResponse
    {
        $this->authorize('forceDelete', $certificate);

        $this->certificates->forceDelete($certificate);

        return redirect()->route('admin.certificates.trash')->with('success', __('Certificate permanently deleted.'));
    }

    /**
     * Stream a QR code PNG encoding this certificate's public verification URL,
     * for the admin to download and place on the printed certificate.
     */
    public function qr(Certificate $certificate): Response
    {
        $this->authorize('view', $certificate);

        $url = route('verify', ['code' => $certificate->verification_code]);

        $qrCode = new QrCode(
            data: $url,
            errorCorrectionLevel: ErrorCorrectionLevel::Quartile,
            size: 320,
            margin: 10,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255),
        );

        $result = (new PngWriter())->write($qrCode);

        return response($result->getString(), 200, [
            'Content-Type' => $result->getMimeType(),
            'Content-Disposition' => 'attachment; filename="'.$certificate->certificate_number.'-qr.png"',
        ]);
    }
}
