<?php

namespace Tests\Feature\Admin;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class EnrollmentImportTest extends TestCase
{
    use CreatesAdminUsers, RefreshDatabase;

    private function csv(string $content): UploadedFile
    {
        return UploadedFile::fake()->createWithContent('enrollments.csv', $content);
    }

    public function test_a_user_without_permissions_cannot_import_enrollments(): void
    {
        $user = $this->userWithoutPermissions();

        $this->actingAs($user)->get(route('admin.enrollments.import'))->assertForbidden();
    }

    public function test_editor_can_import_enrollments_matched_by_student_and_course_code(): void
    {
        $editor = $this->editor();
        $student = Student::factory()->create();
        $course = Course::factory()->create();

        $csv = "Student Code,Course Code,Session,Roll Number,Registration Number,Admission Date,Completion Date,Grade,Grade Point,Grade Scale,Result Status\n"
            ."{$student->student_code},{$course->course_code},2025-2026,101,REG-001,2025-01-15,,,,,pending\n";

        $response = $this->actingAs($editor)->post(route('admin.enrollments.import.process'), [
            'file' => $this->csv($csv),
        ]);

        $response->assertRedirect(route('admin.enrollments.index'));

        $enrollment = Enrollment::firstOrFail();
        $this->assertSame($student->id, $enrollment->student_id);
        $this->assertSame($course->id, $enrollment->course_id);
        $this->assertSame('2025-2026', $enrollment->session);
        $this->assertSame('101', $enrollment->roll_number);
        $this->assertSame('pending', $enrollment->result_status);
    }

    public function test_a_row_with_an_unknown_student_code_is_skipped_and_reported(): void
    {
        $editor = $this->editor();
        $course = Course::factory()->create();

        $csv = "Student Code,Course Code,Session,Roll Number,Registration Number,Admission Date,Completion Date,Grade,Grade Point,Grade Scale,Result Status\n"
            ."STU-DOES-NOT-EXIST,{$course->course_code},2025-2026,101,,,,,,,pending\n";

        $response = $this->actingAs($editor)->post(route('admin.enrollments.import.process'), [
            'file' => $this->csv($csv),
        ]);

        $response->assertRedirect(route('admin.enrollments.import'));
        $response->assertSessionHas('import_failures');
        $this->assertSame(0, Enrollment::count());
    }

    public function test_a_duplicate_roll_number_in_the_same_course_and_session_is_rejected(): void
    {
        $editor = $this->editor();
        $course = Course::factory()->create();
        $existingStudent = Student::factory()->create();
        Enrollment::factory()->create([
            'student_id' => $existingStudent->id,
            'course_id' => $course->id,
            'session' => '2025-2026',
            'roll_number' => '101',
        ]);

        $newStudent = Student::factory()->create();

        $csv = "Student Code,Course Code,Session,Roll Number,Registration Number,Admission Date,Completion Date,Grade,Grade Point,Grade Scale,Result Status\n"
            ."{$newStudent->student_code},{$course->course_code},2025-2026,101,,,,,,,pending\n";

        $response = $this->actingAs($editor)->post(route('admin.enrollments.import.process'), [
            'file' => $this->csv($csv),
        ]);

        $response->assertSessionHas('import_failures');
        $this->assertSame(1, Enrollment::count());
    }
}
