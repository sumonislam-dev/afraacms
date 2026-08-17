<?php

namespace Tests\Feature\Admin;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class CourseTest extends TestCase
{
    use CreatesAdminUsers, RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $course = Course::factory()->create();

        $this->get(route('admin.courses.index'))->assertRedirect(route('login'));
        $this->get(route('admin.courses.edit', $course))->assertRedirect(route('login'));
    }

    public function test_a_user_without_permissions_cannot_view_courses(): void
    {
        $user = $this->userWithoutPermissions();

        $this->actingAs($user)->get(route('admin.courses.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.courses.create'))->assertForbidden();
    }

    public function test_editor_can_view_the_course_list(): void
    {
        $editor = $this->editor();
        Course::factory()->create(['course_name' => 'Electrical Installation Course']);

        $this->actingAs($editor)
            ->get(route('admin.courses.index'))
            ->assertOk()
            ->assertSee('Electrical Installation Course');
    }

    public function test_editor_can_add_a_course_with_an_auto_generated_code(): void
    {
        $editor = $this->editor();

        $response = $this->actingAs($editor)->post(route('admin.courses.store'), [
            'course_name' => 'Mobile Servicing Course',
            'duration' => '02 Years',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('admin.courses.index'));

        $course = Course::where('course_name', 'Mobile Servicing Course')->firstOrFail();
        $this->assertNotEmpty($course->course_code);
        $this->assertStringStartsWith('CRS-', $course->course_code);
    }

    public function test_creating_a_course_requires_a_name_and_duration(): void
    {
        $editor = $this->editor();

        $this->actingAs($editor)
            ->post(route('admin.courses.store'), ['status' => 'active'])
            ->assertSessionHasErrors(['course_name', 'duration']);
    }

    public function test_editor_can_update_a_course(): void
    {
        $editor = $this->editor();
        $course = Course::factory()->create();

        $response = $this->actingAs($editor)->put(route('admin.courses.update', $course), [
            'course_name' => $course->course_name,
            'duration' => $course->duration,
            'status' => 'inactive',
        ]);

        $response->assertRedirect(route('admin.courses.index'));
        $this->assertSame('inactive', $course->fresh()->status);
    }

    public function test_editor_can_delete_a_course(): void
    {
        $editor = $this->editor();
        $course = Course::factory()->create();

        $this->actingAs($editor)
            ->delete(route('admin.courses.destroy', $course))
            ->assertRedirect(route('admin.courses.index'));

        $this->assertSoftDeleted($course);
    }

    public function test_a_course_with_enrollments_cannot_be_permanently_deleted(): void
    {
        $editor = $this->editor();
        $course = Course::factory()->create();
        Enrollment::factory()->create(['course_id' => $course->id]);
        $course->delete();

        $this->actingAs($editor)
            ->delete(route('admin.courses.force-delete', $course))
            ->assertRedirect();

        $this->assertSoftDeleted($course);
    }
}
