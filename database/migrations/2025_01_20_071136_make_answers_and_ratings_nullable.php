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
        Schema::table('company_evaluation_ratings', function (Blueprint $table) {
            $table->integer('score')->nullable()->change();
        });
        Schema::table('intern_evaluation_ratings', function (Blueprint $table) {
            $table->integer('score')->nullable()->change();
        });
        Schema::table('report_ratings', function (Blueprint $table) {
            $table->integer('score')->nullable()->change();
        });
        Schema::table('company_evaluation_answers', function (Blueprint $table) {
            $table->text('answer')->nullable()->change();
        });
        Schema::table('intern_evaluation_answers', function (Blueprint $table) {
            $table->text('answer')->nullable()->change();
        });
        Schema::table('report_answers', function (Blueprint $table) {
            $table->text('answer')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_evaluation_ratings', function (Blueprint $table) {
            $table->integer('score')->change();
        });
        Schema::table('intern_evaluation_ratings', function (Blueprint $table) {
            $table->integer('score')->change();
        });
        Schema::table('report_ratings', function (Blueprint $table) {
            $table->integer('score')->change();
        });
        Schema::table('company_evaluation_answers', function (Blueprint $table) {
            $table->text('answer')->change();
        });
        Schema::table('intern_evaluation_answers', function (Blueprint $table) {
            $table->text('answer')->change();
        });
        Schema::table('report_answers', function (Blueprint $table) {
            $table->text('answer')->change();
        });
    }
};
