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
        Schema::table('open_questions', function (Blueprint $table) {
            $table->text('tooltip')->nullable();
        });
        Schema::table('rating_questions', function (Blueprint $table) {
            $table->text('tooltip')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('open_questions', function (Blueprint $table) {
            $table->dropColumn('tooltip');
        });
        Schema::table('rating_questions', function (Blueprint $table) {
            $table->dropColumn('tooltip');
        });
    }
};
