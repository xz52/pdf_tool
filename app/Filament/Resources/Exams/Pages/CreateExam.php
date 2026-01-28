<?php

namespace App\Filament\Resources\Exams\Pages;

use App\Filament\Resources\Exams\ExamResource;
use App\Models\GlobalQuestion;
use Filament\Resources\Pages\CreateRecord;

class CreateExam extends CreateRecord
{
    protected static string $resource = ExamResource::class;

    public ?array $data;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['is_active'] = true;

        $this->data = $data;

        return $data;
    }

    public function afterCreate(): void
    {
        $record = $this->record;
        if($this->data['is_random'])
        {
            $questions = GlobalQuestion::where('subject_id', $this->data['subject_id'])->inRandomOrder()->limit($this->data['total_questions'])->get();

            foreach ($questions as $question) {
                $record->questions()->create($question->toArray());
            }
            
        }
        else{

            $question_ids = $this->data['questions'];

            $questions = GlobalQuestion::whereIn('id', $question_ids)->get();

            foreach ($questions as $question) {
                $record->questions()->create($question->toArray());
            }
        }
    }
}
