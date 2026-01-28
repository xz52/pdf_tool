# DOCX Question Import Tool for Laravel & Filament

This repository contains the core logic and documentation for a high-accuracy DOCX question import system integrated with Filament Admin.

## Features
- **Dual-Strategy Parsing**: Handles both cell-based (Block Mode) and row-based (Grid Mode) question layouts.
- **Stage 2 Specialization**: Splitting concept rows into multiple individual MCQ and True/False variations.
- **Bold Detection**: Automatically identifies correct answers by parsing DOCX bold formatting.
- **Data Integrity**: Strips HTML, metadata prefixes ("Edit", "Variation"), and standardizes question formatting.
- **Filament Integration**: Custom transformer for "Question Models" repeater with support for Multiple Choice and True/False types.

## File Structure
- `app/Services/DocxParserService.php`: The core parsing engine.
- `app/Console/Commands/ImportQuestions.php`: CLI command for batch importing DOCX files.
- `app/Filament/Resources/GlobalQuestions/Pages/EditGlobalQuestion.php`: Form data transformer.
- `docs/walkthrough.md`: Detailed proof of work and import results (2,851 questions).
- `docs/project_development_guide.md`: In-depth study guide of the architecture and logic.

## Summary of Work
This tool was built to resolve complex parsing issues across three distinct DOCX formats, achieving a zero-error import of nearly 3,000 questions with proper subject mapping and clean metadata.
