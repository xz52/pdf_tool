# Project Development & Architecture Guide

This guide details the journey, architecture, and logic of the Question Import System. It is designed for study purposes to understand how the feature was built and how it works under the hood.

---

## 1. Project Evolution

### Phase 1: Python-Based Parsing (Legacy)
Initially, the project relied on a Python script (`pdf_question_parser.py`) to extract text from PDF and RTF files.
- **Workflow**: `run_import.sh` -> `python script` -> `.json file` -> `php artisan import` -> Database.
- **Limitation**: Required a Python environment with specific libraries installed, which was not available in your environment.

### Phase 2: Native PHP Solution (Current)
To remove the external dependency, we migrated the logic for modern formats (`.docx`) entirely to PHP.
- **Workflow**: `run_import.sh` -> `php artisan questions:import` (native parsing) -> Database.
- **Key Library**: `phpoffice/phpword` was installed via Composer to read DOCX files programmatically.

---

## 2. System Architecture

The system is composed of three main layers:

### A. The Orchestrator (`run_import.sh`)
This shell script is the entry point. It automates the batch processing.
- **Discovery**: Uses `find` to locate all `.docx`, `.pdf`, and `.rtf` files in the `PDFs/` subdirectory.
- **Routing**:
    - **DOCX**: Directly calls Laravel (`php artisan questions:import file.docx`).
    - **PDF/RTF**: Falls back to the legacy Python parser (if available).
- **Looping**: It iterates through *every* file found, so you can drop 100 files in the folder and it will process them all one by one.

### B. The Command (`app/Console/Commands/ImportQuestions.php`)
This is the interface between the CLI and the application logic.
- **Input**: Accepts a file path.
- **Router**: Checks the file extension.
    - If `.json`: Treats it as pre-parsed data (legacy flow).
    - If `.docx`: Instantiates `DocxParserService` to parse it on the fly.
- **Subject Mapping**: `guessSubjectFromFilename()` analyzes the filename using Regex patterns (e.g., `/PPL.*STAGE\s*(\d+)/i`) to assign the question to the correct Subject ID (e.g., "PPL Stage 1").
- **Database Transaction**: Wraps the insertion in a transaction (`DB::beginTransaction`) so partial failures don't corrupt the database.

### C. The Parser Service (`app/Services/DocxParserService.php`)
This is where the magic happens. It translates a binary DOCX file into structured Question data.

---

## 3. Deep Dive: Parsing Logic

### Step 1: Loading the File
We use `IOFactory::load($path)` from the `phpword` library. This reads the DOCX XML structure.
> **Challenge**: The library had a `Class Not Found` error due to Composer autoloader issues.
> **Solution**: We implemented a manual registration check in the service:
> ```php
> if (!class_exists('PhpOffice\PhpWord\Autoloader')) {
>     require_once base_path('.../Autoloader.php');
>     \PhpOffice\PhpWord\Autoloader::register();
> }
> ```

### Step 2: Extracting Content
DOCX files are made of Sections -> Tables -> Rows -> Cells -> TextRuns.
We iterate through every cell in the document tables.
The parser employs a **Dual-Strategy** approach: 
1.  **Block Mode**: Checks if a single cell contains a complete "Question Block" (e.g., "1. Question... A. Option"). Used extensively in Stage 1 and Stage 3.
2.  **Grid Mode**: Used for complex table structures (Stage 2). A single row is split into **multiple individual questions**:
    - **Cell 1**: Parsed as a Multiple Choice Question (MCQ).
    - **Cells 2-4**: Parsed as separate True/False variation questions related to the same concept.

### Step 3: Bold Detection (The "Correct Answer" Logic)
This was the most critical requirement. The correct answer is marked by **bold text** in the document.
- We don't just extract plain text; we iterate through `TextRun` elements.
- We check `$textRun->getFontStyle()->isBold()`.
- We build a map of "Bold Ranges" (start index, end index) for the raw text of the cell.
- Later, when we split the options (A, B, C), we check if the text of an option falls within a "Bold Range" to mark it as correct.

Raw extraction includes noise like "1.", "Question 1:", "Edit", "Variation X:", and trailing artifacts.
- **Prefix Cleaning**: Removes "Edit ", "Variation 1:", and question numbering from the start of strings.
- **True/False Cleaning**: Automatically strips the trailing "TrueFalse" word from statement variations.
- **HTML/Entities**: Uses `strip_tags` and `html_entity_decode` to ensure plain text output.

---

## 4. Subject Auto-Detection

The `ImportQuestions.php` command uses Regular Expressions to map filenames to subjects.

| File Name Pattern | Detected Subject |
|-------------------|------------------|
| `PPL TEST GUIDE STAGE 1.docx` | `PPL Stage 1` |
| `Private Pilot Stage 2.docx` | `Private Pilot Stage 2` |

If the detected name exists in the `subjects` table, it uses that ID. If not, the import for that file stops with an error (unless we added auto-creation logic).

---

## 5. Summary of Key Files

| File | Purpose |
|------|---------|
| `run_import.sh` | Finds files and runs the appropriate command for each. |
| `app/Console/Commands/ImportQuestions.php` | Main entry point for the Laravel logic. Handles database saving. |
| `app/Services/DocxParserService.php` | Reads DOCX, finds bold text, parses options, returns Array. |
| `composer.json` | Manages dependencies like `phpoffice/phpword`. |

---

## 6. How to Study This Code
1.  **Start with the Service**: Read `DocxParserService::parseTable`. This is the core algorithm.
2.  **Trace the Execution**: Follow the path from `run_import.sh` -> `ImportQuestions::handle` -> `DocxParserService::parse`.
3.  **Experiment**: Try creating a simplified DOCX with just one question and run the import to see how different formatting affects the output.
