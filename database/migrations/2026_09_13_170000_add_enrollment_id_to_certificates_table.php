<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Links the standalone Certificate record to an Enrollment (Student +
 * Course), so a certificate can be issued for a real enrolled student
 * instead of always retyping a free-text recipient name/program. Nullable
 * and unique: a certificate may still have no enrollment (manual/non-course
 * certificates keep working exactly as before), and an enrollment may back
 * at most one certificate.
 *
 * Also adds the manual-only fields (session, grade, completion_date, roll/
 * registration number): an enrollment-issued certificate gets its equivalent
 * of these from the Enrollment itself, but a manually entered recipient had
 * no equivalent of what the verify page shows for an enrollment (session,
 * grade, completion date). Bundled into this same migration since both sets
 * of columns land on the same table via the same Schema::table() operation.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->foreignId('enrollment_id')->nullable()->after('project_id')->unique()->constrained()->nullOnDelete();
            $table->string('session')->nullable()->after('program');
            $table->string('grade')->nullable()->after('session');
            $table->date('completion_date')->nullable()->after('grade');
            $table->string('roll_number')->nullable()->after('completion_date');
            $table->string('registration_number')->nullable()->after('roll_number');
        });
    }

    /**
     * The enrollment_id FK and unique index drops are deliberately separate
     * try/catch steps ahead of the column drop rather than one
     * dropConstrainedForeignId() call: MySQL implicitly drops an index when
     * its column goes, but SQLite's drop-column table-rebuild needs the
     * unique index gone first, or it fails trying to recreate an index over
     * a now-missing column.
     */
    public function down(): void
    {
        if (Schema::hasColumn('certificates', 'enrollment_id')) {
            try {
                Schema::table('certificates', function (Blueprint $table) {
                    $table->dropForeign(['enrollment_id']);
                });
            } catch (Throwable) {
                // Constraint didn't exist under this name; nothing to drop.
            }

            try {
                Schema::table('certificates', function (Blueprint $table) {
                    $table->dropUnique(['enrollment_id']);
                });
            } catch (Throwable) {
                // Index didn't exist under this name; nothing to drop.
            }

            Schema::table('certificates', function (Blueprint $table) {
                $table->dropColumn('enrollment_id');
            });
        }

        Schema::table('certificates', function (Blueprint $table) {
            $table->dropColumn(['session', 'grade', 'completion_date', 'roll_number', 'registration_number']);
        });
    }
};
