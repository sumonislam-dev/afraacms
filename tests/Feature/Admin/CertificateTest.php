<?php

namespace Tests\Feature\Admin;

use App\Models\Certificate;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class CertificateTest extends TestCase
{
    use CreatesAdminUsers, RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $certificate = Certificate::factory()->create();

        $this->get(route('admin.certificates.index'))->assertRedirect(route('login'));
        $this->get(route('admin.certificates.show', $certificate))->assertRedirect(route('login'));
        $this->get(route('admin.certificates.edit', $certificate))->assertRedirect(route('login'));
    }

    public function test_a_user_without_permissions_cannot_view_certificates(): void
    {
        $user = $this->userWithoutPermissions();

        $this->actingAs($user)->get(route('admin.certificates.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.certificates.create'))->assertForbidden();
    }

    public function test_editor_can_view_the_certificate_list(): void
    {
        $editor = $this->editor();
        Certificate::factory()->create(['recipient_name' => 'Jane Doe']);

        $this->actingAs($editor)
            ->get(route('admin.certificates.index'))
            ->assertOk()
            ->assertSee('Jane Doe');
    }

    public function test_editor_can_issue_a_certificate_with_auto_generated_identifiers(): void
    {
        $editor = $this->editor();
        $project = Project::factory()->create();

        $response = $this->actingAs($editor)->post(route('admin.certificates.store'), [
            'project_id' => $project->id,
            'recipient_name' => 'John Smith',
            'program' => 'Web Development Training',
            'issued_at' => '2026-01-15',
            'status' => 'valid',
        ]);

        $response->assertRedirect(route('admin.certificates.index'));

        $certificate = Certificate::where('recipient_name', 'John Smith')->firstOrFail();
        $this->assertNotEmpty($certificate->certificate_number);
        $this->assertNotEmpty($certificate->verification_code);
        $this->assertSame(32, strlen($certificate->verification_code));
        $this->assertSame($project->id, $certificate->project_id);
    }

    public function test_a_user_without_permissions_cannot_create_a_certificate(): void
    {
        $user = $this->userWithoutPermissions();

        $this->actingAs($user)->post(route('admin.certificates.store'), [
            'recipient_name' => 'Sneaky Person',
            'issued_at' => '2026-01-15',
            'status' => 'valid',
        ])->assertForbidden();

        $this->assertDatabaseMissing('certificates', ['recipient_name' => 'Sneaky Person']);
    }

    public function test_creating_a_certificate_requires_recipient_name_and_issued_at(): void
    {
        $editor = $this->editor();

        $this->actingAs($editor)
            ->post(route('admin.certificates.store'), ['status' => 'valid'])
            ->assertSessionHasErrors(['recipient_name', 'issued_at']);
    }

    public function test_editor_can_revoke_a_certificate(): void
    {
        $editor = $this->editor();
        $certificate = Certificate::factory()->create(['status' => 'valid']);

        $response = $this->actingAs($editor)->put(route('admin.certificates.update', $certificate), [
            'recipient_name' => $certificate->recipient_name,
            'issued_at' => $certificate->issued_at->format('Y-m-d'),
            'status' => 'revoked',
        ]);

        $response->assertRedirect(route('admin.certificates.index'));
        $this->assertSame('revoked', $certificate->fresh()->status);
    }

    public function test_editor_can_delete_a_certificate(): void
    {
        $editor = $this->editor();
        $certificate = Certificate::factory()->create();

        $this->actingAs($editor)
            ->delete(route('admin.certificates.destroy', $certificate))
            ->assertRedirect(route('admin.certificates.index'));

        $this->assertNull(Certificate::find($certificate->id));
        $this->assertSoftDeleted($certificate);
    }

    public function test_editor_can_restore_a_trashed_certificate(): void
    {
        $editor = $this->editor();
        $certificate = Certificate::factory()->create();
        $certificate->delete();

        $this->actingAs($editor)
            ->post(route('admin.certificates.restore', $certificate))
            ->assertRedirect(route('admin.certificates.trash'));

        $this->assertNotSoftDeleted($certificate);
    }

    public function test_the_qr_endpoint_streams_a_png_image(): void
    {
        $editor = $this->editor();
        $certificate = Certificate::factory()->create();

        $response = $this->actingAs($editor)->get(route('admin.certificates.qr', $certificate));

        $response->assertOk();
        $this->assertSame('image/png', $response->headers->get('Content-Type'));
    }

    public function test_the_certificate_show_page_displays_the_certificate(): void
    {
        $editor = $this->editor();
        $certificate = Certificate::factory()->create(['recipient_name' => 'Jane Doe']);

        $this->actingAs($editor)
            ->get(route('admin.certificates.show', $certificate))
            ->assertOk()
            ->assertSee('Jane Doe')
            ->assertSee($certificate->certificate_number);
    }

    public function test_the_certificate_show_page_displays_a_revoked_certificate(): void
    {
        $editor = $this->editor();
        $certificate = Certificate::factory()->create(['status' => 'revoked']);

        $this->actingAs($editor)
            ->get(route('admin.certificates.show', $certificate))
            ->assertOk()
            ->assertSee('Revoked');
    }

    public function test_a_user_without_permissions_cannot_view_a_certificate(): void
    {
        $user = $this->userWithoutPermissions();
        $certificate = Certificate::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.certificates.show', $certificate))
            ->assertForbidden();
    }
}
