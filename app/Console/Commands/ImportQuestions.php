<?php

namespace App\Console\Commands;

use App\Models\GlobalQuestion;
use App\Models\Subject;
use App\Services\DocxParserService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportQuestions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'questions:import {file} {--subject=} {--dry-run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import questions from JSON or DOCX file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->argument('file');
        $subjectName = $this->option('subject');
        $dryRun = $this->option('dry-run');

        // Check if file exists
        if (!file_exists($file)) {
            $this->error("File not found: {$file}");
            return 1;
        }

        // Parse questions
        $questions = [];
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        if ($extension === 'json') {
            $this->info("Reading JSON file...");
            $jsonContent = file_get_contents($file);
            $data = json_decode($jsonContent, true);
            if (!$data || !isset($data['questions'])) {
                $this->error("Invalid JSON format");
                return 1;
            }
            $questions = $data['questions'];
        } elseif ($extension === 'docx') {
            $this->info("Parsing DOCX file...");
            try {
                $parser = new DocxParserService();
                $questions = $parser->parse($file);
            } catch (\Exception $e) {
                $this->error("Failed to parse DOCX: " . $e->getMessage());
                return 1;
            }
        } else {
            $this->error("Unsupported file format: {$extension}. Only JSON and DOCX are supported.");
            return 1;
        }

        $this->info("Found " . count($questions) . " questions");

        // Determine subject
        $subject = null;
        if ($subjectName) {
            $subject = Subject::where('name', 'LIKE', "%{$subjectName}%")->first();
            if (!$subject) {
                $this->error("Subject not found: {$subjectName}");
                $this->info("Available subjects:");
                Subject::all()->each(function($s) {
                    $this->line("  - {$s->name} (ID: {$s->id})");
                });
                return 1;
            }
        } else {
            // Try to extract subject from filename
            $subject = $this->guessSubjectFromFilename($file);
            
            if (!$subject) {
                $this->error("Could not determine subject. Please specify with --subject option");
                $this->info("Available subjects:");
                Subject::all()->each(function($s) {
                    $this->line("  - {$s->name} (ID: {$s->id})");
                });
                return 1;
            }
        }

        $this->info("Using subject: {$subject->name} (ID: {$subject->id})");

        if ($dryRun) {
            $this->warn("DRY RUN MODE - No data will be saved");
            $this->displaySampleQuestions($questions, 3);
            return 0;
        }

        // Import questions
        $imported = 0;
        $skipped = 0;
        $errors = 0;

        $progressBar = $this->output->createProgressBar(count($questions));
        $progressBar->start();

        DB::beginTransaction();
        try {
            foreach ($questions as $questionData) {
                try {
                    // Check if question already exists (by question_text)
                    $exists = GlobalQuestion::where('question_text', $questionData['question_text'])
                        ->where('subject_id', $subject->id)
                        ->exists();

                    if ($exists) {
                        $skipped++;
                        $progressBar->advance();
                        continue;
                    }

                    // Create question
                    GlobalQuestion::create([
                        'question_text' => $questionData['question_text'],
                        'explanation' => $questionData['explanation'] ?? '...',
                        'options' => $questionData['options'],
                        'correct_option' => $questionData['correct_option'],
                        'subject_id' => $subject->id,
                        'type' => $questionData['type'] ?? 'multiple_choice',
                    ]);

                    $imported++;
                } catch (\Exception $e) {
                    $errors++;
                    $this->newLine();
                    $this->error("Error importing question: " . $e->getMessage());
                    $this->line("Question: " . substr($questionData['question_text'], 0, 50) . "...");
                }

                $progressBar->advance();
            }

            DB::commit();
            $progressBar->finish();
            $this->newLine(2);

            // Summary
            $this->info("Import completed!");
            $this->table(
                ['Status', 'Count'],
                [
                    ['Imported', $imported],
                    ['Skipped (duplicates)', $skipped],
                    ['Errors', $errors],
                    ['Total', count($questions)],
                ]
            );

        } catch (\Exception $e) {
            DB::rollBack();
            $progressBar->finish();
            $this->newLine(2);
            $this->error("Import failed: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Guess subject from filename
     */
    private function guessSubjectFromFilename(string $filename): ?Subject
    {
        // Extract filename without path
        $basename = basename($filename);
        $basenameNoExt = pathinfo($basename, PATHINFO_FILENAME);
        
        // Common patterns
        $patterns = [
            '/Private.*Pilot.*Stage\s*(\d+)/i' => 'Private Pilot Stage $1',
            '/Commercial.*Pilot.*Stage\s*(\d+)/i' => 'Commercial Pilot Stage $1',
            '/Instrument.*Pilot.*Stage\s*(\d+)/i' => 'Instrument Pilot Stage $1',
            '/Private.*Pilot/i' => 'Private Pilot',
            '/Commercial.*Pilot/i' => 'Commercial Pilot',
            '/Instrument.*Pilot/i' => 'Instrument Pilot',
            '/Stage\s*(\d+)/i' => 'STAGE $1',
            '/PPL.*TEST.*GUIDE.*STAGE\s*(\d+)/i' => 'STAGE $1', 
            '/PPL.*STAGE\s*(\d+)/i' => 'STAGE $1',
        ];

        foreach ($patterns as $pattern => $template) {
            if (preg_match($pattern, $basenameNoExt, $matches)) {
                $subjectName = $template;
                // Replace $1, $2 etc with captured groups
                for ($i = 1; $i < count($matches); $i++) {
                    $subjectName = str_replace('$' . $i, $matches[$i], $subjectName);
                }
                
                // Try to find subject
                // If subject not found, maybe create it? 
                // User said "Subject* and in this we will Choose the subject name based on the file"
                // Let's search strict first.
                $subject = Subject::where('name', 'LIKE', "%{$subjectName}%")->first();
                // Auto-create subject if it doesn't exist
                return Subject::firstOrCreate(['name' => $subjectName]);
                
                // If not found, try to search for just the generic name?
            }
        }

        return null;
    }

    /**
     * Display sample questions for dry run
     */
    private function displaySampleQuestions(array $questions, int $count = 3)
    {
        $this->newLine();
        $this->info("Sample questions:");
        $this->newLine();

        $samples = array_slice($questions, 0, min($count, count($questions)));

        foreach ($samples as $i => $q) {
            $this->line("Question " . ($i + 1) . " [{$q['type']}]:");
            $this->line("  Text: " . $q['question_text']);
            $this->line("  Options:");
            foreach ($q['options'] as $idx => $opt) {
                $marker = ($idx === $q['correct_option']) ? ' ✓' : '';
                $this->line("    " . chr(65 + $idx) . ": {$opt}{$marker}");
            }
            $this->line("  Explanation: " . substr($q['explanation'] ?? '...', 0, 100));
            $this->newLine();
        }
    }
}
