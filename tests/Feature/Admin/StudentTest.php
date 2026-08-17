<?php

namespace Tests\Feature\Admin;

use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class StudentTest extends TestCase
{
    use CreatesAdminUsers, RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $student = Student::factory()->create();

        $this->get(route('admin.students.index'))->assertRedirect(route('login'));
        $this->get(route('admin.students.edit', $student))->assertRedirect(route('login'));
    }

    public function test_a_user_without_permissions_cannot_view_students(): void
    {
        $user = $this->userWithoutPermissions();

        $this->actingAs($user)->get(route('admin.students.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.students.create'))->assertForbidden();
    }

    public function test_editor_can_view_the_student_list(): void
    {
        $editor = $this->editor();
        Student::factory()->create(['name' => 'Jane Doe']);

        $this->actingAs($editor)
            ->get(route('admin.students.index'))
            ->assertOk()
            ->assertSee('Jane Doe');
    }

    public function test_editor_can_add_a_student_with_an_auto_generated_code(): void
    {
        $editor = $this->editor();

        $response = $this->actingAs($editor)->post(route('admin.students.store'), [
            'name' => 'John Smith',
            'father_name' => 'Robert Smith',
            'mother_name' => 'Mary Smith',
            'date_of_birth' => '2005-08-13',
            'phone' => '01700000000',
            'email' => 'john@example.com',
        ]);

        $response->assertRedirect(route('admin.students.index'));

        $student = Student::where('name', 'John Smith')->firstOrFail();
        $this->assertNotEmpty($student->student_code);
        $this->assertStringStartsWith('STU-', $student->student_code);
    }

    public function test_a_user_without_permissions_cannot_create_a_student(): void
    {
        $user = $this->userWithoutPermissions();

        $this->actingAs($user)->post(route('admin.students.store'), [
            'name' => 'Sneaky Person',
            'father_name' => 'X',
            'mother_name' => 'Y',
            'date_of_birth' => '2000-01-01',
        ])->assertForbidden();

        $this->assertDatabaseMissing('students', ['name' => 'Sneaky Person']);
    }

    public function test_creating_a_student_requires_name_and_parent_names(): void
    {
        $editor = $this->editor();

        $this->actingAs($editor)
            ->post(route('admin.students.store'), ['date_of_birth' => '2000-01-01'])
            ->assertSessionHasErrors(['name', 'father_name', 'mother_name']);
    }

    public function test_editor_can_update_a_student(): void
    {
        $editor = $this->editor();
        $student = Student::factory()->create();

        $response = $this->actingAs($editor)->put(route('admin.students.update', $student), [
            'name' => 'Updated Name',
            'father_name' => $student->father_name,
            'mother_name' => $student->mother_name,
            'date_of_birth' => $student->date_of_birth->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('admin.students.index'));
        $this->assertSame('Updated Name', $student->fresh()->name);
    }

    public function test_editor_can_delete_a_student(): void
    {
        $editor = $this->editor();
        $student = Student::factory()->create();

        $this->actingAs($editor)
            ->delete(route('admin.students.destroy', $student))
            ->assertRedirect(route('admin.students.index'));

        $this->assertNull(Student::find($student->id));
        $this->assertSoftDeleted($student);
    }

    public function test_editor_can_restore_a_trashed_student(): void
    {
        $editor = $this->editor();
        $student = Student::factory()->create();
        $student->delete();

        $this->actingAs($editor)
            ->post(route('admin.students.restore', $student))
            ->assertRedirect(route('admin.students.trash'));

        $this->assertNotSoftDeleted($student);
    }

    public function test_a_student_with_enrollments_cannot_be_permanently_deleted(): void
    {
        $editor = $this->editor();
        $student = Student::factory()->create();
        Enrollment::factory()->create(['student_id' => $student->id]);
        $student->delete();

        $this->actingAs($editor)
            ->delete(route('admin.students.force-delete', $student))
            ->assertRedirect();

        $this->assertSoftDeleted($student);
    }
}
