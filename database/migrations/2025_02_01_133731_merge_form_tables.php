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
        Schema::dropIfExists('company_evaluation_answers');
        Schema::dropIfExists('company_evaluation_ratings');
        Schema::dropIfExists('company_evaluations');
        Schema::dropIfExists('company_evaluation_statuses');

        Schema::dropIfExists('intern_evaluation_answers');
        Schema::dropIfExists('intern_evaluation_ratings');
        Schema::dropIfExists('intern_evaluations');
        Schema::dropIfExists('intern_evaluation_statuses');

        Schema::dropIfExists('report_answers');
        Schema::dropIfExists('report_ratings');
        Schema::dropIfExists('reports');
        Schema::dropIfExists('report_statuses');

        Schema::dropIfExists('rating_questions');
        Schema::dropIfExists('open_ended_questions');

        Schema::create('forms', function (Blueprint $table) {
            $table->id();

            $table->string('form_name');
        });

        Schema::create('rating_categories', function (Blueprint $table) {
            $table->id();

            $table->string('category_name');
        });

        Schema::create('open_questions', function (Blueprint $table) {
            $table->id();

            $table->text('question');
        });

        Schema::create('rating_questions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('rating_category_id')->constrained();

            $table->text('question');
            $table->text('max_score');
        });

        Schema::create('form_open_questions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('form_id')->constrained();
            $table->foreignId('open_questions')->constrained();
        });

        Schema::create('form_rating_questions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('form_id')->constrained();
            $table->foreignId('rating_questions')->constrained();
        });

        Schema::create('open_answers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('open_question_id')->constrained();
            $table->foreignId('user_id')->constrained();

            $table->text('answer')->nullable();
        });

        Schema::create('rating_scores', function (Blueprint $table) {
            $table->id();

            $table->foreignId('rating_question_id')->constrained();
            $table->foreignId('user_id')->constrained();

            $table->integer('score')->nullable();
        });

        Schema::create('form_statuses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('rating_question_id')->constrained();
            $table->foreignId('user_id')->constrained();

            $table->enum('status', ['rejected', 'unsubmitted', 'submitted', 'validated'])->default('unsubmitted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
