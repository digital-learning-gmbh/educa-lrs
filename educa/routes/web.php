<?php

use App\Http\Controllers\ActorController;
use App\Http\Controllers\ObjectController;
use App\Http\Controllers\StatementController;
use App\Http\Controllers\VerbController;
use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\WelcomeController::class, 'landingPage']);
Route::get('/install', [\App\Http\Controllers\WelcomeController::class, 'install']);

Route::prefix("api")->middleware(['xapi'])->group(function () {

    // Actor Routes
    Route::post('/actors', [ActorController::class, 'store']); // Create actor
    Route::get('/actors/{id}', [ActorController::class, 'show']); // Retrieve actor
    Route::put('/actors/{id}', [ActorController::class, 'update']); // Update actor
    Route::delete('/actors/{id}', [ActorController::class, 'destroy']); // Delete actor

    // Verb Routes
    Route::get('/verbs', [VerbController::class, 'index']); // List all verbs
    Route::post('/verbs', [VerbController::class, 'store']); // Create a new verb
    Route::get('/verbs/{id}', [VerbController::class, 'show']); // Get a specific verb
    Route::put('/verbs/{id}', [VerbController::class, 'update']); // Update a verb
    Route::delete('/verbs/{id}', [VerbController::class, 'destroy']); // Delete a verb

    // Object Routes
    Route::get('/objects', [ObjectController::class, 'index']); // List all objects
    Route::post('/objects', [ObjectController::class, 'store']); // Create a new object
    Route::get('/objects/{id}', [ObjectController::class, 'show']); // Get a specific object
    Route::put('/objects/{id}', [ObjectController::class, 'update']); // Update an object
    Route::delete('/objects/{id}', [ObjectController::class, 'destroy']); // Delete an object

    // Statements
    Route::get('/statements', [StatementController::class, 'index']);
    Route::post('/statements', [StatementController::class, 'store']);
    Route::post('/statements/bulk', [StatementController::class, 'bulkStore']);
    Route::post('/statements/filter', [StatementController::class, 'filter']);
});
