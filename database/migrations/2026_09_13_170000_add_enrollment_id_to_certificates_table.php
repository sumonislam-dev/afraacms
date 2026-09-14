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
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->foreignId('enrollment_id')->nullable()->after('project_id')->unique()->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropConstrainedForeignId('enrollment_id');
        });
    }
};
