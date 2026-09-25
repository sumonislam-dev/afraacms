<?php

namespace Tests\Feature\Admin;

use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class StudentImportTest extends TestCase
{
    use CreatesAdminUsers, RefreshDatabase;

    private function csv(string $content): UploadedFile
    {
        return UploadedFile::fake()->createWithContent('students.csv', $content);
    }

    public function test_a_user_without_permissions_cannot_import_students(): void
    {
        $user = $this->userWithoutPermissions();

        $this->actingAs($user)->get(route('admin.students.import'))->assertForbidden();
    }

    public function test_editor_can_import_students_from_a_csv_file(): void
    {
        $editor = $this->editor();

        $csv = "Name,Father Name,Mother Name,Date of Birth,Phone,Email,Address\n"
            ."Jane Doe,John Doe,Mary Doe,2005-01-31,01700000000,jane@example.com,Dhaka\n"
            ."John Roe,Sam Roe,Ann Roe,2003-06-15,,,\n";

        $response = $this->actingAs($editor)->post(route('admin.students.import.process'), [
            'file' => $this->csv($csv),
        ]);

        $response->assertRedirect(route('admin.students.index'));
        $this->assertSame(2, Student::count());

        $jane = Student::where('name', 'Jane Doe')->firstOrFail();
        $this->assertSame('John Doe', $jane->father_name);
        $this->assertSame('Mary Doe', $jane->mother_name);
        $this->assertSame('2005-01-31', $jane->date_of_birth->format('Y-m-d'));
        $this->assertSame('jane@example.com', $jane->email);
        $this->assertNotNull($jane->student_code);
    }

    public function test_rows_missing_required_fields_are_skipped_and_reported(): void
    {
        $editor = $this->editor();

        $csv = "Name,Father Name,Mother Name,Date of Birth,Phone,Email,Address\n"
            ."Jane Doe,John Doe,Mary Doe,2005-01-31,,,\n"
            .",Missing Name,Mother,2005-01-31,,,\n";

        $response = $this->actingAs($editor)->post(route('admin.students.import.process'), [
            'file' => $this->csv($csv),
        ]);

        $response->assertRedirect(route('admin.students.import'));
        $response->assertSessionHas('import_failures');

        $this->assertSame(1, Student::count());
        $this->assertSame('Jane Doe', Student::first()->name);
    }
}
