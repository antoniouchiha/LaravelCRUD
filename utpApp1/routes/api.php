<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UtpController;

Route::apiResource('dato', UtpController::class);
Route::get('dato', [UtpController::class, 'index']);
Route::get('dato/{id}', [UtpController::class, 'show']);
Route::post('dato', [UtpController::class, 'store']);
Route::post('dato/{id}', [UtpController::class, 'update']);
Route::delete('dato/{id}', [UtpController::class, 'destroy']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



