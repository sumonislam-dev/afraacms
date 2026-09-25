<?php

namespace App\Http\Controllers\Admin;

use App\CMS\Exports\StudentsTemplateExport;
use App\CMS\Imports\StudentsImport;
use App\CMS\Services\StudentService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ImportStudentsRequest;
use App\Http\Requests\Admin\StoreStudentRequest;
use App\Http\Requests\Admin\UpdateStudentRequest;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StudentController extends Controller
{
    public function __construct(private readonly StudentService $students)
    {
        $this->authorizeResource(Student::class, 'student');
    }

    /**
     * Display a listing of the students.
     */
    public function index(): View
    {
        $students = Student::query()
            ->when(request('search'), fn ($query, $search) => $query
                ->where('name', 'like', "%{$search}%")
                ->orWhere('student_code', 'like', "%{$search}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.students.index', compact('students'));
    }

    /**
     * Show the form for creating a new student.
     */
    public function create(): View
    {
        return view('admin.students.create');
    }

    /**
     * Store a newly created student.
     */
    public function store(StoreStudentRequest $request): RedirectResponse
    {
        $this->students->create($request->validated());

        return redirect()->route('admin.students.index')->with('success', __('Student added successfully.'));
    }

    /**
     * Show the form for editing the given student.
     */
    public function edit(Student $student): View
    {
        return view('admin.students.edit', compact('student'));
    }

    /**
     * Update the given student.
     */
    public function update(UpdateStudentRequest $request, Student $student): RedirectResponse
    {
        $this->students->update($student, $request->validated());

        return redirect()->route('admin.students.index')->with('success', __('Student updated successfully.'));
    }

    /**
     * Delete the given student.
     */
    public function destroy(Student $student): RedirectResponse
    {
        $this->students->delete($student);

        return redirect()->route('admin.students.index')->with('success', __('Student deleted successfully.'));
    }

    /**
     * Show the bulk-import form.
     */
    public function import(): View
    {
        $this->authorize('create', Student::class);

        return view('admin.students.import');
    }

    /**
     * Download a blank template with the exact columns import() expects.
     */
    public function template(): BinaryFileResponse
    {
        $this->authorize('create', Student::class);

        return Excel::download(new StudentsTemplateExport, 'students-import-template.xlsx');
    }

    /**
     * Process an uploaded spreadsheet: valid rows are saved immediately,
     * invalid ones are skipped and reported back row-by-row rather than
     * aborting the whole file.
     */
    public function processImport(ImportStudentsRequest $request): RedirectResponse
    {
        $import = new StudentsImport;

        Excel::import($import, $request->file('file'));

        $failures = collect($import->failures())->map(fn ($failure) => [
            'row' => $failure->row(),
            'attribute' => $failure->attribute(),
            'errors' => $failure->errors(),
        ]);

        if ($failures->isEmpty()) {
            return redirect()->route('admin.students.index')->with('success', __('Students imported successfully.'));
        }

        return redirect()->route('admin.students.import')->with('import_failures', $failures->all());
    }

    /**
     * Display the trashed (soft-deleted) students.
     */
    public function trash(): View
    {
        $this->authorize('viewAny', Student::class);

        $students = Student::onlyTrashed()
            ->when(request('search'), fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->orderByDesc('deleted_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.students.trash', compact('students'));
    }

    /**
     * Restore a trashed student.
     */
    public function restore(Student $student): RedirectResponse
    {
        $this->authorize('restore', $student);

        $this->students->restore($student);

        return redirect()->route('admin.students.trash')->with('success', __('Student restored successfully.'));
    }

    /**
     * Permanently delete a trashed student.
     */
    public function forceDelete(Student $student): RedirectResponse
    {
        $this->authorize('forceDelete', $student);

        if (! $this->students->forceDelete($student)) {
            return back()->with('error', __('This student still has enrollment records and cannot be permanently deleted.'));
        }

        return redirect()->route('admin.students.trash')->with('success', __('Student permanently deleted.'));
    }
}
