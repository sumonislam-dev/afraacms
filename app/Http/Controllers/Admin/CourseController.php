<?php

namespace App\Http\Controllers\Admin;

use App\CMS\Services\CourseService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCourseRequest;
use App\Http\Requests\Admin\UpdateCourseRequest;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function __construct(private readonly CourseService $courses)
    {
        $this->authorizeResource(Course::class, 'course');
    }

    /**
     * Display a listing of the courses.
     */
    public function index(): View
    {
        $courses = Course::query()
            ->when(request('search'), fn ($query, $search) => $query
                ->where('course_name', 'like', "%{$search}%")
                ->orWhere('course_code', 'like', "%{$search}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new course.
     */
    public function create(): View
    {
        return view('admin.courses.create');
    }

    /**
     * Store a newly created course.
     */
    public function store(StoreCourseRequest $request): RedirectResponse
    {
        $this->courses->create($request->validated());

        return redirect()->route('admin.courses.index')->with('success', __('Course added successfully.'));
    }

    /**
     * Show the form for editing the given course.
     */
    public function edit(Course $course): View
    {
        return view('admin.courses.edit', compact('course'));
    }

    /**
     * Update the given course.
     */
    public function update(UpdateCourseRequest $request, Course $course): RedirectResponse
    {
        $this->courses->update($course, $request->validated());

        return redirect()->route('admin.courses.index')->with('success', __('Course updated successfully.'));
    }

    /**
     * Delete the given course.
     */
    public function destroy(Course $course): RedirectResponse
    {
        $this->courses->delete($course);

        return redirect()->route('admin.courses.index')->with('success', __('Course deleted successfully.'));
    }

    /**
     * Display the trashed (soft-deleted) courses.
     */
    public function trash(): View
    {
        $this->authorize('viewAny', Course::class);

        $courses = Course::onlyTrashed()
            ->when(request('search'), fn ($query, $search) => $query->where('course_name', 'like', "%{$search}%"))
            ->orderByDesc('deleted_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.courses.trash', compact('courses'));
    }

    /**
     * Restore a trashed course.
     */
    public function restore(Course $course): RedirectResponse
    {
        $this->authorize('restore', $course);

        $this->courses->restore($course);

        return redirect()->route('admin.courses.trash')->with('success', __('Course restored successfully.'));
    }

    /**
     * Permanently delete a trashed course.
     */
    public function forceDelete(Course $course): RedirectResponse
    {
        $this->authorize('forceDelete', $course);

        if (! $this->courses->forceDelete($course)) {
            return back()->with('error', __('This course still has enrollment records and cannot be permanently deleted.'));
        }

        return redirect()->route('admin.courses.trash')->with('success', __('Course permanently deleted.'));
    }
}
