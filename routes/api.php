<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExceptionLogsController;
use App\Http\Controllers\ServerMetricsController;
use App\Repositories\ExceptionLogGroupRepository;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

if (ExceptionLogGroupRepository::isEnabled()) {
    Route::post('/exceptions', ExceptionLogsController::class)->name('api.exceptions');
}

Route::post('/hardware', ServerMetricsController::class)->name('api.hardware');
