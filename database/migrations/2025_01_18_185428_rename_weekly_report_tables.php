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
        Schema::dropIfExists('weekly_report_ratings');
        Schema::dropIfExists('weekly_report_answers');
        Schema::dropIfExists('weekly_reports');
        Schema::dropIfExists('weekly_report_statuses');

        Schema::create('report_statuses', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Foreign key
            $table->integer('student_number');
            $table->foreign('student_number')->references('student_number')->on('students');
            $table->foreignId('supervisor_id')->constrained();

            // Other attributes
            $table->enum('status', ['rejected', 'unsubmitted', 'submitted', 'validated'])->default('unsubmitted');
        });

        Schema::create('reports', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Foreign keys
            $table->foreignId('report_status_id')->constrained();

            // Other keys
            $table->integer('total_hours');

            // Timestamps
            $table->timestamps();
        });

        Schema::create('report_answers', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Foreign keys
            $table->foreignId('report_id')->constrained();
            $table->foreignId('open_ended_question_id')->constrained();

            // Other keys
            $table->text('answer');
        });

        Schema::create('report_ratings', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Foreign keys
            $table->foreignId('report_id')->constrained();
            $table->foreignId('rating_question_id')->constrained();

            // Other keys
            $table->integer('score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_ratings');
        Schema::dropIfExists('report_answers');
        Schema::dropIfExists('reports');
        Schema::dropIfExists('report_statuses');

        Schema::create('weekly_report_statuses', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Foreign key
            $table->integer('student_number');
            $table->foreign('student_number')->references('student_number')->on('students');
            $table->foreignId('supervisor_id')->constrained();

            // Other attributes
            $table->integer('week');
            $table->enum('status', ['rejected', 'unsubmitted', 'submitted', 'validated'])->default('unsubmitted')->change();
        });

        Schema::create('weekly_reports', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Foreign keys
            $table->foreignId('weekly_report_status_id')->constrained();

            // Other keys
            $table->integer('total_hours');

            // Timestamps
            $table->timestamps();
        });

        Schema::create('weekly_report_answers', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Foreign keys
            $table->foreignId('weekly_report_id')->constrained();
            $table->foreignId('open_ended_question_id')->constrained();

            // Other keys
            $table->text('answer');
        });

        Schema::create('weekly_report_ratings', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Foreign keys
            $table->foreignId('weekly_report_id')->constrained();
            $table->foreignId('rating_question_id')->constrained();

            // Other keys
            $table->integer('score');
        });
    }
};
