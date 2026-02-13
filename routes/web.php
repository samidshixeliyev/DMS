<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LegalActController;
use App\Http\Controllers\ActTypeController;
use App\Http\Controllers\IssuingAuthorityController;
use App\Http\Controllers\ExecutorController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ExecutionNoteController;

Route::get('/', function () {
    return view('welcome');
});

// Legal Acts Routes - Export routes BEFORE resource routes
Route::get('legal-acts/export/excel', [LegalActController::class, 'exportExcel'])->name('legal-acts.export.excel');
Route::get('legal-acts/export/word', [LegalActController::class, 'exportWord'])->name('legal-acts.export.word');
Route::resource('legal-acts', LegalActController::class);

// Act Types Routes
Route::resource('act-types', ActTypeController::class);

// Departments Routes
Route::resource('departments', DepartmentController::class);

// Executors Routes
Route::resource('executors', ExecutorController::class);

// Issuing Authorities Routes
Route::resource('issuing-authorities', IssuingAuthorityController::class);

// Execution Notes Routes
Route::resource('execution-notes', ExecutionNoteController::class);

// Logout route (if using default Laravel auth)
Route::post('logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');