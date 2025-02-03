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
            $table->string('short_name');
            $table->enum('phase', ['pre', 'during', 'post'])->default('pre');
            $table->dateTime('deadline')->nullable();
        });

        Schema::create('rating_categories', function (Blueprint $table) {
            $table->id();

            $table->text('category_name');
        });

        Schema::create('open_questions', function (Blueprint $table) {
            $table->id();

            $table->text('question');
        });

        Schema::create('rating_questions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('rating_category_id')->constrained();

            $table->text('criterion');
            $table->integer('max_score')->nullable();
        });

        Schema::create('form_open_questions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('form_id')->constrained();
            $table->foreignId('open_question_id')->constrained();
        });

        Schema::create('form_rating_questions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('form_id')->constrained();
            $table->foreignId('rating_question_id')->constrained();
        });

        Schema::create('form_statuses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('form_id')->constrained();
            $table->foreignId('user_id')->constrained();    // Associates to user answering form

            $table->enum('status', ['rejected', 'unsubmitted', 'submitted', 'validated'])->default('unsubmitted');
        });

        Schema::create('form_answers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('form_status_id')->constrained();
            $table->foreignId('evaluated_user_id')->references('id')->on('users');
        });

        Schema::create('open_answers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('form_answer_id')->constrained();
            $table->foreignId('open_question_id')->constrained();

            $table->text('answer')->nullable();
        });

        Schema::create('rating_scores', function (Blueprint $table) {
            $table->id();

            $table->foreignId('form_answer_id')->constrained();
            $table->foreignId('rating_question_id')->constrained();

            $table->integer('score')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // TODO: Migration Rollbacks
    }
};
