<?php

use App\Http\Controllers\Api\V1\Teacher\AuthController;
use App\Http\Controllers\Api\V1\Teacher\DashboardController;
use App\Http\Controllers\Api\V1\Teacher\ClassesController;
use App\Http\Controllers\Api\V1\Teacher\SubjectsController;
use App\Http\Controllers\Api\V1\Teacher\AttendanceController;
use App\Http\Controllers\Api\V1\Teacher\MarksController;
use App\Http\Controllers\Api\V1\Teacher\QuestionsController;
use App\Http\Controllers\Api\V1\Teacher\NotesController;
use App\Http\Controllers\Api\V1\Teacher\ProfileController;
use Illuminate\Support\Facades\Route;

// ── Public ──────────────────────────────────────────────────────────
Route::post('/v1/login', [AuthController::class, 'login']);
Route::post('/v1/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// ── Authenticated teacher routes ────────────────────────────────────
Route::prefix('v1')->middleware(['auth:sanctum', 'api.teacher'])->group(function () {

    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);

    Route::prefix('teacher')->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index']);

        // Classes
        Route::get('/classes', [ClassesController::class, 'index']);
        Route::get('/classes/{class}', [ClassesController::class, 'show']);

        // Subjects
        Route::get('/subjects', [SubjectsController::class, 'index']);

        // Attendance
        Route::get('/attendance', [AttendanceController::class, 'index']);
        Route::get('/attendance/mark', [AttendanceController::class, 'markForm']);
        Route::post('/attendance', [AttendanceController::class, 'store']);

        // Marks
        Route::get('/marks', [MarksController::class, 'index']);
        Route::get('/marks/sheet', [MarksController::class, 'sheet']);
        Route::post('/marks', [MarksController::class, 'store']);

        // Questions
        Route::get('/questions', [QuestionsController::class, 'index']);
        Route::post('/questions', [QuestionsController::class, 'store']);
        Route::put('/questions/{question}', [QuestionsController::class, 'update']);
        Route::delete('/questions/{question}', [QuestionsController::class, 'destroy']);

        // Notes
        Route::get('/notes', [NotesController::class, 'index']);
        Route::post('/notes', [NotesController::class, 'store']);
        Route::delete('/notes/{note}', [NotesController::class, 'destroy']);
    });
});
