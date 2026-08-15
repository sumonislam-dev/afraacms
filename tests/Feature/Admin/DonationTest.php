<?php

namespace Tests\Feature\Admin;

use App\Mail\DonationReceipt;
use App\Models\Donation;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class DonationTest extends TestCase
{
    use CreatesAdminUsers, RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $donation = Donation::factory()->create();

        $this->get(route('admin.donations.index'))->assertRedirect(route('login'));
        $this->get(route('admin.donations.edit', $donation))->assertRedirect(route('login'));
    }

    public function test_a_user_without_permissions_cannot_view_donations(): void
    {
        $user = $this->userWithoutPermissions();

        $this->actingAs($user)->get(route('admin.donations.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.donations.create'))->assertForbidden();
    }

    public function test_editor_can_view_the_donation_list(): void
    {
        $editor = $this->editor();
        Donation::factory()->create(['donor_name' => 'Jane Doe']);

        $this->actingAs($editor)
            ->get(route('admin.donations.index'))
            ->assertOk()
            ->assertSee('Jane Doe');
    }

    public function test_editor_can_record_a_donation_and_a_receipt_email_is_sent(): void
    {
        Mail::fake();

        $editor = $this->editor();
        $project = Project::factory()->create();

        $response = $this->actingAs($editor)->post(route('admin.donations.store'), [
            'project_id' => $project->id,
            'donor_name' => 'John Smith',
            'donor_email' => 'john@example.com',
            'amount' => '150.50',
            'currency' => 'BDT',
            'method' => 'bank_transfer',
            'donated_at' => '2026-01-15',
            'status' => 'completed',
        ]);

        $response->assertRedirect(route('admin.donations.index'));

        $donation = Donation::where('donor_name', 'John Smith')->firstOrFail();
        $this->assertNotEmpty($donation->receipt_number);
        $this->assertNotNull($donation->receipt_sent_at);

        Mail::assertSent(DonationReceipt::class, fn ($mail) => $mail->hasTo('john@example.com')
            && $mail->donation->is($donation));
    }

    public function test_no_receipt_is_sent_when_no_donor_email_is_given(): void
    {
        Mail::fake();

        $editor = $this->editor();

        $this->actingAs($editor)->post(route('admin.donations.store'), [
            'donor_name' => 'Anonymous Cash Donor',
            'amount' => '50',
            'currency' => 'BDT',
            'method' => 'cash',
            'donated_at' => '2026-01-15',
            'status' => 'completed',
        ]);

        $donation = Donation::where('donor_name', 'Anonymous Cash Donor')->firstOrFail();
        $this->assertNull($donation->receipt_sent_at);

        Mail::assertNothingSent();
    }

    public function test_a_user_without_permissions_cannot_create_a_donation(): void
    {
        $user = $this->userWithoutPermissions();

        $this->actingAs($user)->post(route('admin.donations.store'), [
            'donor_name' => 'Sneaky Person',
            'amount' => '10',
            'currency' => 'BDT',
            'method' => 'cash',
            'donated_at' => '2026-01-15',
            'status' => 'completed',
        ])->assertForbidden();

        $this->assertDatabaseMissing('donations', ['donor_name' => 'Sneaky Person']);
    }

    public function test_creating_a_donation_requires_donor_name_and_amount(): void
    {
        $editor = $this->editor();

        $this->actingAs($editor)
            ->post(route('admin.donations.store'), ['currency' => 'BDT', 'method' => 'cash', 'donated_at' => '2026-01-15', 'status' => 'completed'])
            ->assertSessionHasErrors(['donor_name', 'amount']);
    }

    public function test_editor_can_update_a_donation(): void
    {
        $editor = $this->editor();
        $donation = Donation::factory()->create(['status' => 'completed']);

        $response = $this->actingAs($editor)->put(route('admin.donations.update', $donation), [
            'donor_name' => $donation->donor_name,
            'amount' => $donation->amount,
            'currency' => $donation->currency,
            'method' => $donation->method,
            'donated_at' => $donation->donated_at->format('Y-m-d'),
            'status' => 'refunded',
        ]);

        $response->assertRedirect(route('admin.donations.index'));
        $this->assertSame('refunded', $donation->fresh()->status);
    }

    public function test_editor_can_delete_a_donation(): void
    {
        $editor = $this->editor();
        $donation = Donation::factory()->create();

        $this->actingAs($editor)
            ->delete(route('admin.donations.destroy', $donation))
            ->assertRedirect(route('admin.donations.index'));

        $this->assertNull(Donation::find($donation->id));
        $this->assertSoftDeleted($donation);
    }

    public function test_editor_can_restore_a_trashed_donation(): void
    {
        $editor = $this->editor();
        $donation = Donation::factory()->create();
        $donation->delete();

        $this->actingAs($editor)
            ->post(route('admin.donations.restore', $donation))
            ->assertRedirect(route('admin.donations.trash'));

        $this->assertNotSoftDeleted($donation);
    }

    public function test_editor_can_resend_a_receipt(): void
    {
        Mail::fake();

        $editor = $this->editor();
        $donation = Donation::factory()->create(['donor_email' => 'donor@example.com']);

        $this->actingAs($editor)
            ->post(route('admin.donations.resend-receipt', $donation))
            ->assertRedirect();

        Mail::assertSent(DonationReceipt::class);
    }

    public function test_resend_receipt_is_rejected_when_no_donor_email_on_file(): void
    {
        Mail::fake();

        $editor = $this->editor();
        $donation = Donation::factory()->withoutEmail()->create();

        $this->actingAs($editor)
            ->post(route('admin.donations.resend-receipt', $donation))
            ->assertRedirect();

        Mail::assertNothingSent();
    }
}
