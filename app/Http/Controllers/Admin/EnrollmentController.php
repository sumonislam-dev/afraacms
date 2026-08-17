<?php

namespace App\Http\Controllers\Admin;

use App\CMS\Services\EnrollmentService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEnrollmentRequest;
use App\Http\Requests\Admin\UpdateEnrollmentRequest;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class EnrollmentController extends Controller
{
    public function __construct(private readonly EnrollmentService $enrollments)
    {
        $this->authorizeResource(Enrollment::class, 'enrollment');
    }

    /**
     * Display a listing of the enrollments.
     */
    public function index(): View
    {
        $enrollments = Enrollment::with(['student', 'course'])
            ->when(request('search'), fn ($query, $search) => $query
                ->where('certificate_number', 'like', "%{$search}%")
                ->orWhere('roll_number', 'like', "%{$search}%")
                ->orWhereHas('student', fn ($q) => $q->where('name', 'like', "%{$search}%")))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.enrollments.index', compact('enrollments'));
    }

    /**
     * Display the issued certificate for the given enrollment - only
     * reachable once a certificate has actually been issued (an enrollment
     * still "not_issued" has no certificate to show).
     */
    public function show(Enrollment $enrollment): View
    {
        abort_if($enrollment->certificate_status === 'not_issued', 404);

        $enrollment->loadMissing(['student', 'course']);

        return view('admin.enrollments.show', compact('enrollment'));
    }

    /**
     * Show the form for creating a new enrollment.
     */
    public function create(): View
    {
        $students = Student::orderBy('name')->get();
        $courses = Course::active()->orderBy('course_name')->get();

        return view('admin.enrollments.create', compact('students', 'courses'));
    }

    /**
     * Store a newly created enrollment.
     */
    public function store(StoreEnrollmentRequest $request): RedirectResponse
    {
        $this->enrollments->create($request->validated());

        return redirect()->route('admin.enrollments.index')->with('success', __('Enrollment added successfully.'));
    }

    /**
     * Show the form for editing the given enrollment.
     */
    public function edit(Enrollment $enrollment): View
    {
        $students = Student::orderBy('name')->get();
        $courses = Course::active()->orderBy('course_name')->get();

        return view('admin.enrollments.edit', compact('enrollment', 'students', 'courses'));
    }

    /**
     * Update the given enrollment.
     */
    public function update(UpdateEnrollmentRequest $request, Enrollment $enrollment): RedirectResponse
    {
        $this->enrollments->update($enrollment, $request->validated());

        return redirect()->route('admin.enrollments.index')->with('success', __('Enrollment updated successfully.'));
    }

    /**
     * Delete the given enrollment.
     */
    public function destroy(Enrollment $enrollment): RedirectResponse
    {
        $this->enrollments->delete($enrollment);

        return redirect()->route('admin.enrollments.index')->with('success', __('Enrollment deleted successfully.'));
    }

    /**
     * Display the trashed (soft-deleted) enrollments.
     */
    public function trash(): View
    {
        $this->authorize('viewAny', Enrollment::class);

        $enrollments = Enrollment::onlyTrashed()
            ->with(['student', 'course'])
            ->when(request('search'), fn ($query, $search) => $query->where('certificate_number', 'like', "%{$search}%"))
            ->orderByDesc('deleted_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.enrollments.trash', compact('enrollments'));
    }

    /**
     * Restore a trashed enrollment.
     */
    public function restore(Enrollment $enrollment): RedirectResponse
    {
        $this->authorize('restore', $enrollment);

        $this->enrollments->restore($enrollment);

        return redirect()->route('admin.enrollments.trash')->with('success', __('Enrollment restored successfully.'));
    }

    /**
     * Permanently delete a trashed enrollment.
     */
    public function forceDelete(Enrollment $enrollment): RedirectResponse
    {
        $this->authorize('forceDelete', $enrollment);

        $this->enrollments->forceDelete($enrollment);

        return redirect()->route('admin.enrollments.trash')->with('success', __('Enrollment permanently deleted.'));
    }

    /**
     * Issue the certificate for a passed enrollment.
     */
    public function issueCertificate(Enrollment $enrollment): RedirectResponse
    {
        $this->authorize('update', $enrollment);

        if (! $this->enrollments->issueCertificate($enrollment)) {
            return back()->with('error', __('Only a passed enrollment without an already-issued certificate can be issued.'));
        }

        return back()->with('success', __('Certificate issued successfully.'));
    }

    /**
     * Revoke an already-issued certificate.
     */
    public function revokeCertificate(Enrollment $enrollment): RedirectResponse
    {
        $this->authorize('update', $enrollment);

        if (! $this->enrollments->revokeCertificate($enrollment)) {
            return back()->with('error', __('This certificate is not currently valid.'));
        }

        return back()->with('success', __('Certificate revoked successfully.'));
    }

    /**
     * Stream a QR code PNG encoding this enrollment's public verification
     * URL, for the admin to download and place on the printed certificate.
     */
    public function qr(Enrollment $enrollment): Response
    {
        $this->authorize('view', $enrollment);

        abort_if(! $enrollment->verification_code, 404);

        $url = route('verify', ['code' => $enrollment->verification_code]);

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
            'Content-Disposition' => 'inline; filename="'.$enrollment->certificate_number.'-qr.png"',
        ]);
    }
}
