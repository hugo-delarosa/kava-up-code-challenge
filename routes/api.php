<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProjectController;
use App\Http\Controllers\API\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


//protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    //for testing purposes only
    Route::get('/protected-route', function () {
        return response()->json([
            'message' => 'You are accessing a protected route',
        ], 200);
    });

    //Resource routes
    Route::apiResources([
        'projects' => ProjectController::class,
        'projects.tasks' => TaskController::class,
    ]);

    //Custom routes
    Route::get('/projects/{project_id}/stats', [ProjectController::class, 'statistics']);
});
