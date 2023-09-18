<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SurveyController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['api'],'prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login'])->name("login");
    Route::post('register', [AuthController::class, 'register'])->name("register");
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('survey', SurveyController::class);
});

Route::group(['prefix' => 'category',
    'name' => 'category.',
    'namespace' => 'App\Http\Controllers\Api'], function () {
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('index', [CategoryController::class, 'index']);
        Route::get('detail', [CategoryController::class, 'detail']);
        Route::post('edit', [CategoryController::class, 'edit']);
        Route::post('create', [CategoryController::class, 'createModel']);
        Route::delete('delete', [CategoryController::class, 'deleteModel']);
        Route::get('export', [CategoryController::class, 'export']);
        Route::get('download-form-excel', [CategoryController::class, 'downloadFormExcel']);
        Route::post('import', [CategoryController::class, 'import']);
    });
});


Route::get('/survey/get-by-slug/{survey:slug}', [SurveyController::class, 'getBySlug']);
Route::post('/survey/{survey}/answer', [SurveyController::class, 'storeAnswer']);
