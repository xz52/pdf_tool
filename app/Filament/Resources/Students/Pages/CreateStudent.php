<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['code'] = $this->generateUniqueStudentCode();
        return $data;
    }

    private function generateUniqueStudentCode(): string
    {
        do {
            $code = strtoupper(Str::random(3) . rand(100, 999));
        } while (\App\Models\Student::where('code', $code)->exists());

        return $code;
    }
}
