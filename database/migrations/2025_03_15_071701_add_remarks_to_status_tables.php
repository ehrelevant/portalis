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
            $table->text('remarks')->nullable();
        });
        Schema::table('submission_statuses', function (Blueprint $table) {
            $table->text('remarks')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('form_statuses', function (Blueprint $table) {
            $table->dropColumn('remarks');
        });
        Schema::table('submission_statuses', function (Blueprint $table) {
            $table->dropColumn('remarks');
        });
    }
};
