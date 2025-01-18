<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('submission_statuses', function (Blueprint $table) {
            $table->enum('status', ['rejected', 'unsubmitted', 'submitted', 'validated'])->default('unsubmitted')->change();
        });

        Schema::table('weekly_report_statuses', function (Blueprint $table) {
            $table->enum('status', ['rejected', 'unsubmitted', 'submitted', 'validated'])->default('unsubmitted')->change();
        });

        Schema::table('intern_evaluation_statuses', function (Blueprint $table) {
            $table->enum('status', ['rejected', 'unsubmitted', 'submitted', 'validated'])->default('unsubmitted')->change();
        });

        Schema::table('company_evaluation_statuses', function (Blueprint $table) {
            $table->enum('status', ['rejected', 'unsubmitted', 'submitted', 'validated'])->default('unsubmitted')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submission_statuses', function (Blueprint $table) {
            $table->enum('status', ['pending', 'submitted', 'validated'])->default('pending')->change();
        });

        Schema::table('weekly_report_statuses', function (Blueprint $table) {
            $table->enum('status', ['pending', 'submitted', 'validated'])->default('pending')->change();
        });

        Schema::table('intern_evaluation_statuses', function (Blueprint $table) {
            $table->enum('status', ['pending', 'submitted', 'validated'])->default('pending')->change();
        });

        Schema::table('company_evaluation_statuses', function (Blueprint $table) {
            $table->enum('status', ['pending', 'submitted', 'validated'])->default('pending')->change();
        });
    }
};
