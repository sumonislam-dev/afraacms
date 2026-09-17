<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Every migration's down() must actually reverse its up() cleanly, or a
 * migrate:rollback later breaks silently - this happened three times in one
 * session on the real dev database (a migration missing down() entirely, a
 * foreign key name drifting after a MyISAM->InnoDB repair migration added it
 * under a different name, and dropConstrainedForeignId() not clearing a
 * unique index before dropping its column). Walking every migration back to
 * zero and forward again here means that class of bug fails the test suite
 * instead of someone's database.
 */
class MigrationRollbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_migration_rolls_back_and_reapplies_cleanly(): void
    {
        $this->artisan('migrate:rollback', ['--step' => 1000])->assertExitCode(0);

        $this->assertFalse(Schema::hasTable('users'));

        $this->artisan('migrate', ['--force' => true])->assertExitCode(0);

        $this->assertTrue(Schema::hasTable('users'));
        $this->assertTrue(Schema::hasTable('certificates'));
        $this->assertTrue(Schema::hasColumn('certificates', 'enrollment_id'));
        $this->assertTrue(Schema::hasTable('story_categories'));
    }
}
