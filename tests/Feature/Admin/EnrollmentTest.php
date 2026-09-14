<?php

namespace Tests\Feature\Admin;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class EnrollmentTest extends TestCase
{
    use CreatesAdminUsers, RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $enrollment = Enrollment::factory()->create();

        $this->get(route('admin.enrollments.index'))->assertRedirect(route('login'));
        $this->get(route('admin.enrollments.edit', $enrollment))->assertRedirect(route('login'));
    }

    public function test_a_user_without_permissions_cannot_view_enrollments(): void
    {
        $user = $this->userWithoutPermissions();

        $this->actingAs($user)->get(route('admin.enrollments.index'))->assertForbidden();
    }

    public function test_editor_can_filter_enrollments_by_result_status(): void
    {
        $editor = $this->editor();
        Enrollment::factory()->create(['result_status' => 'pending']);
        Enrollment::factory()->passed()->create();

        $response = $this->actingAs($editor)->get(route('admin.enrollments.index', ['result_status' => 'passed']));

        $response->assertOk();
        $this->assertCount(1, $response->viewData('enrollments'));
        $this->assertSame('passed', $response->viewData('enrollments')->first()->result_status);
    }

    public function test_editor_can_filter_enrollments_by_session(): void
    {
        $editor = $this->editor();
        Enrollment::factory()->create(['session' => '2023-2024']);
        Enrollment::factory()->create(['session' => '2024-2025']);

        $response = $this->actingAs($editor)->get(route('admin.enrollments.index', ['session' => '2024-2025']));

        $response->assertOk();
        $this->assertCount(1, $response->viewData('enrollments'));
        $this->assertSame('2024-2025', $response->viewData('enrollments')->first()->session);
    }

    public function test_editor_can_filter_enrollments_by_course(): void
    {
        $editor = $this->editor();
        $courseA = Course::factory()->create();
        $courseB = Course::factory()->create();
        Enrollment::factory()->create(['course_id' => $courseA->id]);
        Enrollment::factory()->create(['course_id' => $courseB->id]);

        $response = $this->actingAs($editor)->get(route('admin.enrollments.index', ['course_id' => $courseA->id]));

        $response->assertOk();
        $this->assertCount(1, $response->viewData('enrollments'));
        $this->assertSame($courseA->id, $response->viewData('enrollments')->first()->course_id);
    }

    public function test_the_index_page_offers_to_issue_a_certificate_for_a_passed_enrollment_without_one(): void
    {
        $editor = $this->editor();
        Enrollment::factory()->passed()->create();

        $this->actingAs($editor)
            ->get(route('admin.enrollments.index'))
            ->assertOk()
            ->assertSee('Issue Certificate')
            ->assertDontSee('View Certificate');
    }

    public function test_the_index_page_links_to_the_certificate_once_issued(): void
    {
        $editor = $this->editor();
        Enrollment::factory()->certificateIssued()->create();

        $this->actingAs($editor)
            ->get(route('admin.enrollments.index'))
            ->assertOk()
            ->assertSee('View Certificate')
            ->assertDontSee('Issue Certificate');
    }

    public function test_editor_can_enroll_a_student_into_a_course(): void
    {
        $editor = $this->editor();
        $student = Student::factory()->create();
        $course = Course::factory()->create();

        $response = $this->actingAs($editor)->post(route('admin.enrollments.store'), [
            'student_id' => $student->id,
            'course_id' => $course->id,
            'session' => '2024-2025',
            'roll_number' => '01',
            'result_status' => 'pending',
        ]);

        $response->assertRedirect(route('admin.enrollments.index'));

        $enrollment = Enrollment::where('student_id', $student->id)->firstOrFail();
        $this->assertSame('not_issued', $enrollment->certificate_status);
        $this->assertNull($enrollment->certificate_number);
    }

    public function test_roll_number_must_be_unique_within_the_same_course_and_session(): void
    {
        $editor = $this->editor();
        $course = Course::factory()->create();
        Enrollment::factory()->create(['course_id' => $course->id, 'session' => '2024-2025', 'roll_number' => '01']);

        $this->actingAs($editor)
            ->post(route('admin.enrollments.store'), [
                'student_id' => Student::factory()->create()->id,
                'course_id' => $course->id,
                'session' => '2024-2025',
                'roll_number' => '01',
                'result_status' => 'pending',
            ])
            ->assertSessionHasErrors(['roll_number']);
    }

    public function test_certificate_cannot_be_issued_until_the_student_has_passed(): void
    {
        $editor = $this->editor();
        $enrollment = Enrollment::factory()->create(['result_status' => 'pending']);

        $this->actingAs($editor)
            ->post(route('admin.enrollments.issue-certificate', $enrollment))
            ->assertRedirect();

        $this->assertSame('not_issued', $enrollment->fresh()->certificate_status);
    }

    public function test_editor_can_issue_a_certificate_for_a_passed_enrollment(): void
    {
        $editor = $this->editor();
        $enrollment = Enrollment::factory()->passed()->create();

        $this->actingAs($editor)
            ->post(route('admin.enrollments.issue-certificate', $enrollment))
            ->assertRedirect();

        $enrollment->refresh();
        $this->assertSame('valid', $enrollment->certificate_status);
        $this->assertNotEmpty($enrollment->certificate_number);
        $this->assertSame(32, strlen($enrollment->verification_code));
    }

    public function test_editor_can_revoke_an_issued_certificate(): void
    {
        $editor = $this->editor();
        $enrollment = Enrollment::factory()->certificateIssued()->create();

        $this->actingAs($editor)
            ->post(route('admin.enrollments.revoke-certificate', $enrollment))
            ->assertRedirect();

        $this->assertSame('revoked', $enrollment->fresh()->certificate_status);
    }

    public function test_issuing_a_certificate_creates_a_linked_certificate_record(): void
    {
        $editor = $this->editor();
        $enrollment = Enrollment::factory()->passed()->create();

        $this->actingAs($editor)->post(route('admin.enrollments.issue-certificate', $enrollment));

        $certificate = Certificate::where('enrollment_id', $enrollment->id)->firstOrFail();
        $this->assertSame('valid', $certificate->status);
        $this->assertSame($enrollment->fresh()->certificate_number, $certificate->certificate_number);
        $this->assertSame($enrollment->fresh()->verification_code, $certificate->verification_code);
        $this->assertSame($enrollment->student->name, $certificate->recipient_name);
        $this->assertSame($enrollment->course->course_name, $certificate->program);
    }

    public function test_revoking_a_certificate_also_revokes_its_linked_certificate_record(): void
    {
        $editor = $this->editor();
        $enrollment = Enrollment::factory()->passed()->create();
        $this->actingAs($editor)->post(route('admin.enrollments.issue-certificate', $enrollment));

        $this->actingAs($editor)->post(route('admin.enrollments.revoke-certificate', $enrollment));

        $certificate = Certificate::where('enrollment_id', $enrollment->id)->firstOrFail();
        $this->assertSame('revoked', $certificate->status);
    }

    public function test_the_qr_endpoint_streams_a_png_image_for_an_issued_certificate(): void
    {
        $editor = $this->editor();
        $enrollment = Enrollment::factory()->certificateIssued()->create();

        $response = $this->actingAs($editor)->get(route('admin.enrollments.qr', $enrollment));

        $response->assertOk();
        $this->assertSame('image/png', $response->headers->get('Content-Type'));
    }

    public function test_the_certificate_show_page_404s_when_no_certificate_has_been_issued(): void
    {
        $editor = $this->editor();
        $enrollment = Enrollment::factory()->passed()->create();

        $this->actingAs($editor)
            ->get(route('admin.enrollments.show', $enrollment))
            ->assertNotFound();
    }

    public function test_the_certificate_show_page_displays_an_issued_certificate(): void
    {
        $editor = $this->editor();
        $enrollment = Enrollment::factory()->certificateIssued()->create();

        $this->actingAs($editor)
            ->get(route('admin.enrollments.show', $enrollment))
            ->assertOk()
            ->assertSee($enrollment->student->name)
            ->assertSee($enrollment->certificate_number);
    }

    public function test_the_certificate_show_page_displays_a_revoked_certificate(): void
    {
        $editor = $this->editor();
        $enrollment = Enrollment::factory()->certificateRevoked()->create();

        $this->actingAs($editor)
            ->get(route('admin.enrollments.show', $enrollment))
            ->assertOk()
            ->assertSee('Revoked');
    }

    public function test_editor_can_delete_an_enrollment(): void
    {
        $editor = $this->editor();
        $enrollment = Enrollment::factory()->create();

        $this->actingAs($editor)
            ->delete(route('admin.enrollments.destroy', $enrollment))
            ->assertRedirect(route('admin.enrollments.index'));

        $this->assertSoftDeleted($enrollment);
    }
}
