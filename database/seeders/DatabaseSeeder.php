<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\Batch;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\ExamAnswer;
use App\Models\ExamQuestion;
use App\Models\GlobalQuestion;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public bool $fake_data = false;

    public function run(): void
    {
        if ($this->fake_data) {
            $this->createFakeData();
        } else {
            $this->createRealData();
        }

        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123456789'),
            'remember_token' => Str::random(10),
        ]);
    }


    private function createFakeData()
    {
        // ------------------------------
        // 1️⃣ Create Batches
        // ------------------------------
        $batches = ['Batch A', 'Batch B', 'Batch C'];
        $batchModels = [];
        foreach ($batches as $name) {
            $batchModels[] = Batch::create(['name' => $name]);
        }

        // ------------------------------
        // 2️⃣ Create Subjects (unique per batch)
        // ------------------------------
        $subjectNames = [
            'Batch A' => ['Algebra', 'Geometry', 'Biology'],
            'Batch B' => ['Physics', 'Chemistry', 'History'],
            'Batch C' => ['Math', 'Computer Science', 'Geography'],
        ];

        $subjectModels = [];
        foreach ($batchModels as $batch) {
            foreach ($subjectNames[$batch->name] as $subName) {
                $subjectModels[] = Subject::create([
                    'name' => $subName,
                    'code' => strtoupper(Str::substr($subName, 0, 4) . rand(100, 999))
                ]);
            }
        }

        // ------------------------------
        // 3️⃣ Link Batches with Subjects
        // ------------------------------
        foreach ($batchModels as $batch) {
            $batchSubjects = array_filter($subjectModels, fn($s) => in_array($s->name, $subjectNames[$batch->name]));
            $batch->subjects()->attach(array_map(fn($s) => $s->id, $batchSubjects));
        }

        // ------------------------------
        // 4️⃣ Create Students
        // ------------------------------
        $studentModels = [];
        foreach ($batchModels as $batch) {
            for ($i = 1; $i <= 5; $i++) {
                $studentModels[] = Student::create([
                    'code' => strtoupper(Str::random(6)),
                    'name' => "Student {$i} {$batch->name}",
                    'batch_id' => $batch->id
                ]);
            }
        }

        // ------------------------------
        // 5️⃣ Create Exams (unique per subject)
        // ------------------------------
        $examModels = [];
        foreach ($subjectModels as $subject) {
            $numExams = rand(1, 3); // كل مادة لها 1-3 امتحانات
            for ($i = 1; $i <= $numExams; $i++) {
                $examModels[] = Exam::create([
                    'subject_id' => $subject->id,
                    'subject' => $subject->name,
                    'duration' => rand(30, 90),
                    'total_questions' => 5,
                    'is_active' => true,
                    'description' => "{$subject->name} Exam {$i}"
                ]);
            }
        }

        // ------------------------------
        // 6️⃣ Create Questions
        // ------------------------------
        foreach ($examModels as $exam) {
            for ($i = 1; $i <= 5; $i++) {
                $options = ["Option A", "Option B", "Option C", "Option D"];
                $correct = rand(0, 3);
                GlobalQuestion::create([
                    'subject_id' => $exam->subject_id,
                    'question_text' => "Question {$i} for {$exam->subject}",
                    'options' => $options,
                    'correct_option' => $correct,
                    'explanation' => "Explanation for question {$i}"
                ]);

                ExamQuestion::create([
                    'exam_id' => $exam->id,
                    'question_text' => "Question {$i} for {$exam->subject}",
                    'options' => $options,
                    'correct_option' => $correct,
                    'explanation' => "Explanation for question {$i}"
                ]);
            }
        }

        // ------------------------------
        // 7️⃣ Create Exam Results & Answers
        // ------------------------------
        foreach ($studentModels as $student) {
            // Each student takes 1 random exam from their batch subjects
            $batchSubjectIds = $student->batch->subjects->pluck('id')->toArray();
            $studentExams = array_filter($examModels, fn($e) => in_array($e->subject_id, $batchSubjectIds));
            $exam = $studentExams[array_rand($studentExams)];

            $examResult = ExamResult::create([
                'student_id' => $student->id,
                'exam_id' => $exam->id,
                'exam_subject' => $exam->subject,
                'score' => 0,
                'total_questions' => $exam->total_questions,
                'submitted_at' => Carbon::now()
            ]);

            $score = 0;
            foreach ($exam->questions as $question) {
                $selected = rand(0, 3);
                $isCorrect = $selected === $question->correct_option;
                if ($isCorrect) $score++;

                ExamAnswer::create([
                    'exam_result_id' => $examResult->id,
                    'question_id' => $question->id,
                    'question_text' => $question->question_text,
                    'options' => $question->options,
                    'selected_option_index' => $selected,
                    'correct_option_index' => $question->correct_option,
                    'is_correct' => $isCorrect,
                    'explanation' => $question->explanation
                ]);
            }

            $examResult->update(['score' => $score]);
        }
    }

    private function createRealData()
    {
        $batch = Batch::firstOrCreate([
            'name' => 'Main Batch',
        ]);
    }
}
