<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('api')->group(function () {
    Route::apiResource('pages', PageController::class);
    Route::get('pages/{pageSlugs}', [PageController::class, 'show'])->where('pageSlugs', '.*');
});
