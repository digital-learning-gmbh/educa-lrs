<?php

use App\Http\Controllers\ActorController;
use App\Http\Controllers\ObjectController;
use App\Http\Controllers\StatementController;
use App\Http\Controllers\VerbController;
use App\Models\Actor;
use App\Models\LearningObject;
use App\Models\Statement;
use App\Models\Verb;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // detect, if migrated, otherwise redirect to install
    if (!\Schema::hasTable('migrations')) {
        return redirect('/install');
    }
    $stats = [
        'actors' => Actor::count(),
        'verbs' => Verb::count(),
        'objects' => LearningObject::count(),
        'statements' => Statement::count(),
    ];

    // Pass statistics to the welcome view
    return view('welcome', compact('stats'));
});

Route::get('/install', function () {
    // Execute the migrations
    Artisan::call('migrate', ['--force' => true]);

    return view('install_finished');
});


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
});
