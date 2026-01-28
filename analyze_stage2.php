<?php
require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpWord\IOFactory;

// Register autoloader
if (!class_exists('PhpOffice\PhpWord\Autoloader')) {
    require_once __DIR__ . '/vendor/phpoffice/phpword/src/PhpWord/Autoloader.php';
    \PhpOffice\PhpWord\Autoloader::register();
}

$file = '../PDFs/PPL TEST GUIDE STAGE 2.docx';
echo "=== ANALYZING STAGE 2 STRUCTURE ===\n\n";

try {
    $phpWord = IOFactory::load($file);
    foreach ($phpWord->getSections() as $sectionIndex => $section) {
        echo "Section $sectionIndex:\n";
        foreach ($section->getElements() as $elemIndex => $element) {
            if ($element instanceof \PhpOffice\PhpWord\Element\Table) {
                echo "  Table found - showing first 3 rows:\n\n";
                
                $rowCount = 0;
                foreach ($element->getRows() as $rowIndex => $row) {
                    if ($rowCount >= 3) break;
                    
                    echo "  Row $rowIndex:\n";
                    $cells = $row->getCells();
                    
                    foreach ($cells as $cellIndex => $cell) {
                        $text = '';
                        foreach ($cell->getElements() as $ce) {
                            if ($ce instanceof \PhpOffice\PhpWord\Element\TextRun) {
                                foreach ($ce->getElements() as $textEl) {
                                    if (method_exists($textEl, 'getText')) {
                                        $text .= $textEl->getText();
                                    }
                                }
                            }
                        }
                        $preview = strlen($text) > 80 ? substr($text, 0, 80) . '...' : $text;
                        echo "    Cell $cellIndex: $preview\n";
                    }
                    echo "\n";
                    $rowCount++;
                }
            }
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
