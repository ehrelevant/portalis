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
        Schema::create('weekly_report_statuses', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Foreign key
            $table->integer('student_number');
            $table->foreign('student_number')->references('student_number')->on('students');
            $table->foreignId('supervisor_id')->constrained();

            // Other attributes
            $table->integer('week');
            $table->enum('status', ['pending', 'submitted', 'validated'])->default('pending');
        });

        Schema::table('weekly_reports', function (Blueprint $table) {
            // Drop redundant columns
            $table->dropForeign(['student_number']);
            $table->dropForeign(['supervisor_id']);
            $table->dropColumn('student_number');
            $table->dropColumn('supervisor_id');
            $table->dropColumn('week');

            // New foreign key
            $table->foreignId('weekly_report_status_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weekly_reports', function (Blueprint $table) {
            $table->dropForeign(['weekly_report_status_id']);
            $table->dropColumn('weekly_report_status_id');

            $table->foreignId('supervisor_id')->constrained();
            $table->integer('student_number');
            $table->foreign('student_number')->references('student_number')->on('students');

            $table->integer('week');
        });

        Schema::dropIfExists('weekly_report_statuses');
    }
};
