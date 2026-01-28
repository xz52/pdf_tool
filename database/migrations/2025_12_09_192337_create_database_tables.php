<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {

        // 1️⃣ batches table
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // 2️⃣ subjects table
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique()->nullable();
            $table->timestamps();
        });

        // 3️⃣ batch_subject pivot table
        Schema::create('batch_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // 4️⃣ students table
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->foreignId('batch_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });

        // 5️⃣ exams table
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->nullable()->constrained()->onDelete('set null');
            $table->string('subject'); // نصي حفظ نسخة
            $table->integer('duration'); // دقائق
            $table->integer('total_questions')->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 6️⃣ questions table
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->text('question_text');
            $table->json('options');
            $table->integer('correct_option');
            $table->text('explanation')->nullable();
            $table->timestamps();
        });

        // 7️⃣ exam_results table
        Schema::create('exam_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->string('exam_subject');
            $table->integer('score');
            $table->integer('total_questions');
            $table->dateTime('submitted_at');
            $table->timestamps();
        });

        // 8️⃣ exam_answers table
        Schema::create('exam_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_result_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->text('question_text');
            $table->json('options');
            $table->integer('selected_option_index');
            $table->integer('correct_option_index');
            $table->boolean('is_correct');
            $table->text('explanation')->nullable();
            $table->timestamps();
        });

    }

    public function down(): void {
        Schema::dropIfExists('exam_answers');
        Schema::dropIfExists('exam_results');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('exams');
        Schema::dropIfExists('students');
        Schema::dropIfExists('batch_subject');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('batches');
    }
};
