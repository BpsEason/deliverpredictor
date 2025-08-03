<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PredictController;
use App\Http\Controllers\MetricsController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// 定義一個路由來轉發預測請求到 ML 服務
Route::post('/predict', [PredictController::class, 'predict']);

// Prometheus metrics 端點
Route::get('/metrics', [MetricsController::class, 'index']);
