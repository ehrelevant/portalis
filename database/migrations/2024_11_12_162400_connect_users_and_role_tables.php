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
        // USERS table
        Schema::table('users', function (Blueprint $table) {
            // Add role_id for polymorphic relations
            $table->string('role_id');

            // Add name columns
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
        });

        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('companies');
        Schema::dropIfExists('supervisors');
        Schema::dropIfExists('faculties');
        Schema::dropIfExists('students');
        Schema::enableForeignKeyConstraints();

        // COMPANY table
        Schema::create('companies', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Other keys
            $table->string('company_name');
        });

        // SUPERVISOR table
        Schema::create('supervisors', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Foreign keys
            $table->foreignId('company_id')->constrained();
        });

        // FACULTY table
        Schema::create('faculties', function (Blueprint $table) {
            // Primary key
            // NOTE: Consider dropping table later if not needed
            $table->id();
        });

        // STUDENT table
        Schema::create('students', function (Blueprint $table) {
            // Primary key
            $table->integer('student_number')->primary();

            // Foreign keys
            $table->foreignId('supervisor_id')->constrained();
            $table->foreignId('faculty_id')->constrained();

            // Other keys
            $table->float('grade', precision: 32)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // USERS table
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role_id');

            $table->dropColumn('first_name');
            $table->dropColumn('middle_name');
            $table->dropColumn('last_name');
        });

        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('companies');
        Schema::dropIfExists('supervisors');
        Schema::dropIfExists('faculties');
        Schema::dropIfExists('students');
        Schema::enableForeignKeyConstraints();

        // COMPANY table
        Schema::create('company', function (Blueprint $table) {
            // Primary key
            $table->id('company_id');

            // Other keys
            $table->string('company_name', length: 120);
        });

        // SUPERVISOR table
        Schema::create('supervisor', function (Blueprint $table) {
            // Primary key
            $table->id('supervisor_number');

            // Foreign keys
            $table->foreignId('company_id')->references('company_id')->on('company');

            // Other keys
            $table->string('first_name', length: 50);
            $table->string('middle_name', length: 50);
            $table->string('last_name', length: 50);
            $table->string('email', length: 320);
        });

        // FACULTY table
        Schema::create('faculty', function (Blueprint $table) {
            // Primary key
            $table->id('class_id');

            // Other keys
            $table->string('first_name', length: 50);
            $table->string('middle_name', length: 50);
            $table->string('last_name', length: 50);
            $table->string('email', length: 320);
        });

        // STUDENT table
        Schema::create('student', function (Blueprint $table) {
            // Primary key
            $table->integer('student_number')->primary();

            // Foreign keys
            $table->foreignId('supervisor_number')->references('supervisor_number')->on('supervisor');
            $table->foreignId('class_id')->references('class_id')->on('faculty');

            // Other keys
            $table->string('first_name', length: 50);
            $table->string('middle_name', length: 50);
            $table->string('last_name', length: 50);
            $table->string('email', length: 320);
            $table->float('grade', precision: 32);
        });
    }
};
