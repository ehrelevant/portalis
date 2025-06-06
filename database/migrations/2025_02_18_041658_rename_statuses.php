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
        Schema::table('form_statuses', function (Blueprint $table) {
            $table->enum('status', ['Returned', 'None', 'For Review', 'Accepted'])->default('None')->change();
        });
        Schema::table('submission_statuses', function (Blueprint $table) {
            $table->enum('status', ['Returned', 'None', 'For Review', 'Accepted'])->default('None')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('form_statuses', function (Blueprint $table) {
            $table->enum('status', ['rejected', 'unsubmitted', 'submitted', 'validated'])->default('unsubmitted')->change();
        });
        Schema::table('submission_statuses', function (Blueprint $table) {
            $table->enum('status', ['rejected', 'unsubmitted', 'submitted', 'validated'])->default('unsubmitted')->change();
        });
    }
};
