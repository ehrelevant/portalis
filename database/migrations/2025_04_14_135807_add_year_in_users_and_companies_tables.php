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
        Schema::table('users', function (blueprint $table) {
            $table->integer('year');
        });

        Schema::table('companies', function (blueprint $table) {
            $table->integer('year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('year');
        });

        Schema::table('companies', function (blueprint $table) {
            $table->dropColumn('year');
        });
    }
};
