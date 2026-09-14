<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The certificate itself now lives entirely in `certificates`, linked via
 * enrollment_id (see the two previous migrations). Enrollment no longer
 * needs its own copies of these - Enrollment::certificate_status/
 * certificate_number/verification_code are now computed accessors reading
 * through the certificate() relation instead of real columns.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropUnique(['certificate_number']);
            $table->dropUnique(['verification_code']);
            $table->dropIndex(['certificate_status']);
            $table->dropColumn(['certificate_number', 'verification_code', 'certificate_status']);
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->string('certificate_number', 50)->nullable()->unique()->after('grade_scale');
            $table->string('verification_code', 100)->nullable()->unique()->after('certificate_number');
            $table->string('certificate_status', 20)->default('not_issued')->index()->after('verification_code');
        });
    }
};
