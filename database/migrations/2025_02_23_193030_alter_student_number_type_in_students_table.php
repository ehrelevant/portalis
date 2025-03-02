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
            $table->dropForeign(['student_number']);
            $table->dropColumn('student_number');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->id('student_number')->change();
            $table->renameColumn('student_number', 'id');

            $table->string('student_number');
        });

        Schema::table('submission_statuses', function (Blueprint $table) {
            $table->foreignId('student_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submission_statuses', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->dropColumn('student_id');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('student_number');

            $table->integer('id')->change();
            $table->renameColumn('student_number', 'id');
        });

        Schema::table('submission_statuses', function (Blueprint $table) {
            $table->integer('student_number');
            $table->foreign('student_number')->references('student_number')->on('students');
        });
    }
};
