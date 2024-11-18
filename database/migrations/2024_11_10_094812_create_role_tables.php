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
        // COMPANY table
        Schema::create('company', function (Blueprint $table) {
            // primary key
            $table->id('company_id');

            // other keys
            $table->string('company_name', length: 120);
        });

        // SUPERVISOR table
        Schema::create('supervisor', function (Blueprint $table) {
            // primary key
            $table->id('supervisor_number');

            // foreign keys
            // todo: try to use constrained() instead of references()->on()
            $table->foreignId('company_id')->references('company_id')->on('company');

            // other keys
            $table->string('first_name', length: 50);
            $table->string('middle_name', length: 50);
            $table->string('last_name', length: 50);
            $table->string('email', length: 320);
        });

        // FACULTY table
        Schema::create('faculty', function (Blueprint $table) {
            // primary key
            $table->id('class_id');

            // other keys
            $table->string('first_name', length: 50);
            $table->string('middle_name', length: 50);
            $table->string('last_name', length: 50);
            $table->string('email', length: 320);
        });

        // STUDENT table
        Schema::create('student', function (Blueprint $table) {
            // primary key
            $table->integer('student_number')->primary();

            // foreign keys
            // todo: try to use constrained() instead of references()->on()
            $table->foreignId('supervisor_number')->references('supervisor_number')->on('supervisor');
            $table->foreignId('class_id')->references('class_id')->on('faculty');

            // other keys
            $table->string('first_name', length: 50);
            $table->string('middle_name', length: 50);
            $table->string('last_name', length: 50);
            $table->string('email', length: 320);
            $table->float('grade', precision: 32);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('company');
        Schema::dropIfExists('supervisor');
        Schema::dropIfExists('faculty');
        Schema::dropIfExists('student');
        Schema::enableForeignKeyConstraints();
    }
};
