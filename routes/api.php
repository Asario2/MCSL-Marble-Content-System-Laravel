<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormController;
use App\Http\Controllers\TablesController;
use App\Http\Controllers\RightsController;
use App\Http\Controllers\SQLUpdateController;
use App\Http\Controllers\CommentController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('/AddFunc', [RightsController::class, 'AddFunction']);
Route::prefix('mysqlops')->group(function () {

    // Datum aus lastmySQLOps.dat
    Route::get('/last', [SQLUpdateController::class, 'last']);

    // Tabellen-Liste (local + online)
    Route::get('/tables/{domain}', [SQLUpdateController::class, 'tables']);

    // Get Diff from DB
    Route::get('/diff/{table}/{domain}', [SQLUpdateController::class, 'diffTable']);

    // Set Ignore_Fields
    Route::get('/ignore/{domain}/{table}/{col}', [SQLUpdateController::class, 'Ignore_Field']);

    // Sync Localhost → Online
    Route::post('/sync', [SQLUpdateController::class, 'sync']);
    // Sync ALL
    Route::post('/sync-to-all', [SQLUpdateController::class, 'syncToAll']);

});



