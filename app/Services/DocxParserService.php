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

            $firstCellData = self::extractCellData($cells[0]);
            
            if (preg_match('/^\d+[\.-]\d+$/', trim($firstCellData['text'])) && count($cells) >= 3) {
                $parsedRowQuestions = self::parseGridRow($cells);
                foreach ($parsedRowQuestions as $q) {
                    $questions[] = $q;
                }
                continue;
            }

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

        // MCQ
        $mcqData = self::extractCellData($cells[1]);
        $mcq = self::parseQuestionBlock($mcqData);
        if ($mcq) $rowQuestions[] = $mcq;

        // T/F
        for ($i = 2; $i < count($cells) && $i < 5; $i++) {
            $varData = self::extractCellData($cells[$i]);
            $text = self::stripHtmlArtifacts($varData['text']);
            
            if (stripos($text, 'True') !== false && stripos($text, 'False') !== false) {
                $tf = self::parseTrueFalseFromCell($varData);
                if ($tf) $rowQuestions[] = $tf;
            } else {
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
        $cleanText = self::cleanQuestionText($text);

        if (empty($cleanText)) return null;

        $correctOption = 0; 
        $foundExplicitBold = false;
        foreach ($boldRanges as $range) {
            $boldWord = trim(strtolower(self::stripHtmlArtifacts($range['text'])));
            if ($boldWord === 'true' || $boldWord === 'yes') {
                $correctOption = 0;
                $foundExplicitBold = true;
                break;
            } elseif ($boldWord === 'false' || $boldWord === 'no') {
                $correctOption = 1;
                $foundExplicitBold = true;
                break;
            }
        }

        if (!$foundExplicitBold) {
             // If any part of the cell is bold, assume it's the correct variation (True)
             $correctOption = empty($boldRanges) ? 1 : 0;
        }

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
        $text = self::stripHtmlArtifacts($text);
        
        // Remove common artifacts from heading
        $text = str_ireplace(['TrueFalse', 'True False'], '', $text);
        
        $text = preg_replace('/^Edit\s+/i', '', $text);
        $text = preg_replace('/^Variation\s+\d+[:\s]*/i', '', $text);
        $text = preg_replace('/^(?:Question\s+)?\d+(?:[\.-]\d+)?[\.:\)]\s*/i', '', $text);
        
        $text = preg_replace('/\s*[A-D][\.\)]\s*$/s', '', $text);
        
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }

    private static function cleanOptionText(string $text): string
    {
        $text = self::stripHtmlArtifacts($text);
        $text = str_ireplace(['.TrueFalse', 'TrueFalse', 'True False'], '', $text);
        $text = preg_replace('/^[A-D\d][\.\)]\s*/', '', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }

    private static function stripHtmlArtifacts(string $text): string
    {
        $text = str_ireplace(['&lt;u&gt;', '&lt;/u&gt;', '&lt;b&gt;', '&lt;/b&gt;', '&lt;i&gt;', '&lt;/i&gt;', '<u>', '</u>', '<b>', '</b>', '<i>', '</i>'], '', $text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return $text;
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
                            if ($style->isBold() || $style->getUnderline() !== 'none') $isBold = true;
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
        $text = self::stripHtmlArtifacts($text);
        return preg_match('/^(?:Question\s+)?\d+(?:[\.-]\d+)?[\.:\)]/i', $text) || 
               preg_match('/[A-D][\.\)]\s*.+/s', $text);
    }

    private static function parseQuestionBlock(array $cellData): ?array
    {
        $rawText = $cellData['text'];
        $boldRanges = $cellData['bold_ranges'];

        $tempText = self::stripHtmlArtifacts($rawText);
        
        // Ensure markers have newlines for easier splitting
        $textWithNewlines = preg_replace('/([A-D][\.\)])/', "\n$1", $tempText);
        $lines = explode("\n", $textWithNewlines);
        
        $questionLines = [];
        $options = [];
        // Improved T/F detection: must have both words and NO option markers
        $isTrueFalse = (stripos($tempText, 'True') !== false && stripos($tempText, 'False') !== false) && !preg_match('/[A-D][\.\)]/', $tempText);
        
        $parsingOptions = false;
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            if (preg_match('/^([A-D])[)\.\s]+(.+)/', $line, $matches)) {
                $parsingOptions = true;
                $isTrueFalse = false;
                $options[] = ['text' => $matches[2], 'letter' => $matches[1]];
            } elseif ($isTrueFalse && (stripos($line, 'True') === 0 || stripos($line, 'False') === 0)) {
                $parsingOptions = true;
                $options[] = ['text' => $line, 'letter' => null];
            } else {
                if (!$parsingOptions) {
                    $questionLines[] = $line;
                } else if (!empty($options)) {
                    $options[count($options) - 1]['text'] .= ' ' . $line;
                }
            }
        }

        if (empty($options) && !$isTrueFalse) return null;

        $questionText = self::cleanQuestionText(implode(' ', $questionLines));
        if (empty($questionText)) return null;

        $correctIndex = 0;
        foreach ($options as $index => $opt) {
            $letter = $opt['letter'];
            $optClean = self::cleanOptionText($opt['text']);
            
            foreach ($boldRanges as $range) {
                $rangeClean = trim(self::stripHtmlArtifacts($range['text']));
                if (empty($rangeClean)) continue;
                
                if ($letter && preg_match('/^' . $letter . '[\.\)\s]/i', $rangeClean)) {
                    $correctIndex = $index;
                    break 2;
                }
                
                if (strlen($optClean) > 3 && (strpos($rangeClean, $optClean) !== false || strpos($optClean, $rangeClean) !== false)) {
                     if (stripos($questionText, $rangeClean) !== false && strlen($rangeClean) > 15) continue;
                     
                     $correctIndex = $index;
                     break 2;
                }
            }
        }

        $finalOptions = array_map(function($o) { return self::cleanOptionText($o['text']); }, $options);
        if ($isTrueFalse && empty($finalOptions)) $finalOptions = ['YES', 'NO'];

        return [
            'question_text' => $questionText,
            'options' => $finalOptions,
            'correct_option' => $correctIndex,
            'explanation' => '...',
            'type' => $isTrueFalse ? 'T_F' : 'choose'
        ];
    }
}
