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
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('phase');

            $table->dropForeign(['supervisor_id']);
            $table->dropColumn('supervisor_id');
            $table->foreignId('supervisor_id')->nullable()->constrained();

            $table->string('wordpress_name')->nullable()->change();
            $table->string('wordpress_email')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('phase');

            $table->dropForeign(['supervisor_id']);
            $table->dropColumn('supervisor_id');
            $table->foreignId('supervisor_id')->constrained();

            $table->string('wordpress_name')->change();
            $table->string('wordpress_email')->change();
        });
    }
};
