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
        Schema::create('submission_statuses', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Foreign key
            $table->integer('student_number');
            $table->foreign('student_number')->references('student_number')->on('students');
            $table->foreignId('requirement_id')->constrained();

            // Other keys
            $table->string('status');
        });

        // Drop unneeded keys
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('filename');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_statuses');

        Schema::table('submissions', function (Blueprint $table) {
            $table->string('status');
            $table->string('filename');
        });
    }
};
