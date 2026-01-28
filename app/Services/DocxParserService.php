<?php

namespace App\Services;

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\Cell;
use PhpOffice\PhpWord\Element\TextRun;

class DocxParserService
{
    public static function parse(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: {$filePath}");
        }

        // Manual autoloader workaround
        if (!class_exists('PhpOffice\PhpWord\Autoloader')) {
            require_once base_path('vendor/phpoffice/phpword/src/PhpWord/Autoloader.php');
            \PhpOffice\PhpWord\Autoloader::register();
        }

        $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);
        $questions = [];

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if ($element instanceof Table) {
                    self::parseTable($element, $questions);
                }
            }
        }

        return $questions;
    }

    private static function parseTable(Table $table, array &$questions)
    {
        foreach ($table->getRows() as $rowIndex => $row) {
            $cells = $row->getCells();
            if (count($cells) < 2) continue;

            // Strategy 1: Grid Mode (Row = Multiple Questions)
            $firstCellData = self::extractCellData($cells[0]);
            
            // Pattern for Stage 2 IDs like "6-1"
            if (preg_match('/^\d+[\.-]\d+$/', trim($firstCellData['text'])) && count($cells) >= 3) {
                $parsedRowQuestions = self::parseGridRow($cells);
                foreach ($parsedRowQuestions as $q) {
                    $questions[] = $q;
                }
                continue;
            }

            // Strategy 2: Block Mode (Cell = Question Block)
            foreach ($cells as $cell) {
                $cellData = self::extractCellData($cell);
                
                if (self::isQuestionBlock($cellData['text'])) {
                    $parsedQuestion = self::parseQuestionBlock($cellData);
                    if ($parsedQuestion) {
                        $questions[] = $parsedQuestion;
                    }
                }
            }
        }
    }

    private static function parseGridRow(array $cells): array
    {
        $rowQuestions = [];

        // Cell 1: Original MCQ with options A, B, C
        $mcqData = self::extractCellData($cells[1]);
        $mcq = self::parseQuestionBlock($mcqData);
        if ($mcq) $rowQuestions[] = $mcq;

        // Cells 2+: Variations (usually True/False)
        for ($i = 2; $i < count($cells) && $i < 5; $i++) {
            $varData = self::extractCellData($cells[$i]);
            $text = $varData['text'];
            
            // Detection: Is it a True/False statement?
            if (stripos($text, 'True') !== false && stripos($text, 'False') !== false) {
                $tf = self::parseTrueFalseFromCell($varData);
                if ($tf) $rowQuestions[] = $tf;
            } else {
                // Otherwise treat as a single block if relevant
                $block = self::parseQuestionBlock($varData);
                if ($block) $rowQuestions[] = $block;
            }
        }

        return $rowQuestions;
    }

    private static function parseTrueFalseFromCell(array $cellData): ?array
    {
        $text = $cellData['text'];
        $boldRanges = $cellData['bold_ranges'];

        // Remove "TrueFalse" artifacts
        $cleanText = preg_replace('/True\s*False$/i', '', $text);
        $cleanText = self::cleanQuestionText($cleanText);

        if (empty($cleanText)) return null;

        // Heuristic: If it has bold text, it's usually the correct variation (True)
        // If it's not bold, it's usually the wrong variation (False)
        $correctOption = empty($boldRanges) ? 1 : 0; // 0=YES(True), 1=NO(False)

        return [
            'question_text' => $cleanText,
            'options' => ['YES', 'NO'],
            'correct_option' => $correctOption,
            'explanation' => '...',
            'type' => 'T_F'
        ];
    }

    private static function cleanQuestionText(string $text): string
    {
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Remove "Edit" prefix
        $text = preg_replace('/^Edit\s+/i', '', $text);
        
        // Remove "Variation X:" prefix
        $text = preg_replace('/^Variation\s+\d+[:\s]*/i', '', $text);

        // Remove ID prefix at start (e.g. "6-1.")
        $text = preg_replace('/^(?:Question\s+)?\d+(?:[\.-]\d+)?[\.:\)]\s*/i', '', $text);
        
        // Remove option letters dangling at the end
        $text = preg_replace('/\s*[A-D][\.\)]\s*$/s', '', $text);
        
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }

    private static function cleanOptionText(string $text): string
    {
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = str_ireplace(['.TrueFalse', 'TrueFalse', 'True False'], '', $text);
        $text = preg_replace('/^[A-D\d][\.\)]\s*/', '', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }

    private static function extractCellData(Cell $cell): array
    {
        $fullText = '';
        $boldRanges = [];
        $currentLength = 0;

        foreach ($cell->getElements() as $element) {
            if ($element instanceof TextRun) {
                foreach ($element->getElements() as $textElement) {
                    if (method_exists($textElement, 'getText')) {
                        $text = $textElement->getText();
                        $length = strlen($text);
                        
                        $isBold = false;
                        if (method_exists($textElement, 'getFontStyle')) {
                            $style = $textElement->getFontStyle();
                            if ($style->isBold()) $isBold = true;
                        }

                        if ($isBold) {
                            $boldRanges[] = ['start' => $currentLength, 'end' => $currentLength + $length, 'text' => $text];
                        }

                        $fullText .= $text;
                        $currentLength += $length;
                    }
                }
                $fullText .= "\n";
                $currentLength += 1;
            }
        }
        
        return ['text' => trim($fullText), 'bold_ranges' => $boldRanges];
    }

    private static function isQuestionBlock(string $text): bool
    {
        // Must start with something like "1." or "6-1." or "Question"
        return preg_match('/^(?:Question\s+)?\d+(?:[\.-]\d+)?[\.:\)]/i', $text) || 
               preg_match('/[A-D][\.\)]\s+.+/s', $text);
    }

    private static function parseQuestionBlock(array $cellData): ?array
    {
        $text = $cellData['text'];
        $boldRanges = $cellData['bold_ranges'];

        // Handle cases where options are concatenated on one line
        if (strpos($text, "\n") === false && preg_match('/[A-D][\.\)]\s+/', $text)) {
            $text = preg_replace('/([A-D][\.\)]\s+)/', "\n$1", $text);
        }

        $lines = explode("\n", $text);
        $questionLines = [];
        $options = [];
        
        // Detection
        $isTrueFalse = (stripos($text, 'True') !== false && stripos($text, 'False') !== false) &&
                       !preg_match('/[A-D][\.\)]/', $text);
        
        $parsingOptions = false;
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            if (preg_match('/^([A-D])[)\.\]\s+(.+)/', $line, $matches)) {
                $parsingOptions = true;
                $isTrueFalse = false;
                $options[] = ['text' => self::cleanOptionText($matches[2]), 'letter' => $matches[1], 'full_line' => $line];
            } elseif ($isTrueFalse && (stripos($line, 'True') === 0 || stripos($line, 'False') === 0)) {
                $parsingOptions = true;
                $options[] = ['text' => $line, 'letter' => null, 'full_line' => $line];
            } else {
                if (!$parsingOptions) {
                    $questionLines[] = $line;
                } else if (!empty($options)) {
                    $idx = count($options) - 1;
                    $options[$idx]['text'] .= ' ' . $line;
                }
            }
        }

        if (empty($options) && !$isTrueFalse) return null;

        $questionText = self::cleanQuestionText(implode(' ', $questionLines));
        if (empty($questionText)) return null;

        $correctIndex = 0;
        foreach ($options as $index => $opt) {
            if (self::isOptionBold($opt['text'], $text, $boldRanges)) {
                $correctIndex = $index;
                break;
            }
        }

        $finalOptions = array_map(function($o) { return $o['text']; }, $options);
        if ($isTrueFalse && empty($finalOptions)) $finalOptions = ['YES', 'NO'];

        return [
            'question_text' => $questionText,
            'options' => $finalOptions,
            'correct_option' => $correctIndex,
            'explanation' => '...',
            'type' => $isTrueFalse ? 'T_F' : 'choose'
        ];
    }

    private static function isOptionBold(string $optionText, string $fullText, array $boldRanges): bool
    {
        $pos = strpos($fullText, $optionText);
        if ($pos === false) return false;
        $end = $pos + strlen($optionText);
        
        foreach ($boldRanges as $range) {
            $overlapStart = max($pos, $range['start']);
            $overlapEnd = min($end, $range['end']);
            if ($overlapStart < $overlapEnd) return true; 
        }
        return false;
    }
}
