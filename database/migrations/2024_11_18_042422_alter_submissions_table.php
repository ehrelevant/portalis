<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropForeign(['student_number']);
            $table->dropForeign(['requirement_id']);
            $table->dropColumn('student_number');
            $table->dropColumn('requirement_id');
            $table->dropColumn('submission_date');

            // New foreign key
            $table->foreignId('submission_status_id')->constrained();

            // Other new attributes
            $table->timestamps();
        });

        Schema::table('submission_statuses', function (Blueprint $table) {
            // Change attribute types
            $table->enum('status', ['pending', 'submitted', 'validated'])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropTimestamps();
            $table->dropForeign(['submission_status_id']);
            $table->dropColumn('submission_status_id');

            $table->foreignId('requirement_id')->constrained();
            $table->integer('student_number');
            $table->foreign('student_number')->references('student_number')->on('students');

            $table->date('submission_date');
        });

        Schema::table('submission_statuses', function (Blueprint $table) {
            $table->integer('status')->change();
        });
    }
};
