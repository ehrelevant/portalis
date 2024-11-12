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
        Schema::rename('company', 'companies');
        Schema::rename('supervisor', 'supervisors');
        Schema::rename('faculty', 'faculties');
        Schema::rename('student', 'students');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('companies', 'company');
        Schema::rename('supervisors', 'supervisor');
        Schema::rename('faculties', 'faculty');
        Schema::rename('students', 'student');
    }
};
