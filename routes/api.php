<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\ExamResultController;


Route::get('/student/{code}', [StudentController::class, 'show']);
Route::get('/student/{code}/exams', [StudentController::class, 'exams']);


Route::get('/exam/{examId}', [ExamController::class, 'show']);


Route::post('/exam/{examId}/submit', [ExamResultController::class, 'submit']);
