<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::post('/data-mining/run', [DashboardController::class, 'runDataMining'])->name('data-mining.run');
Route::get('/data-mining/status', [DashboardController::class, 'checkMiningStatus'])->name('data-mining.status');
