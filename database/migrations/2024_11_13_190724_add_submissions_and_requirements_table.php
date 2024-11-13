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
        Schema::create('requirements', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Other keys
            $table->string('requirement_name');
            $table->date('due_date')->nullable();
        });

        Schema::create('submissions', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Foreign keys
            $table->integer('student_number');
            $table->foreign('student_number')->references('student_number')->on('students');
            $table->foreignId('requirement_id')->constrained();

            // Other keys
            $table->string('status');
            $table->date('submission_date');

            $table->integer('revision_number');
            $table->integer('submission_number');

            $table->string('filename');
            $table->string('filepath');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
        Schema::dropIfExists('requirements');
    }
};
