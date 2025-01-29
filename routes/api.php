<?php

use App\Http\Controllers\CommentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AdminController;

Route::middleware('auth:sanctum')->group(function(){
    //User Routes
    Route::post('/logout',[UserController::class,'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // Post Routes
    Route::get('/posts',[PostController::class, 'index']);
    Route::get('/posts/{id}',[PostController::class, 'indexOne']);
    Route::post('/posts',[PostController::class, 'store']);
    Route::put('/posts/{id}',[PostController::class, 'update']);
    Route::delete('/posts/{id}',[PostController::class, 'destroy']);

    Route::post('/comments',[CommentController::class,'store']);
    Route::get('/comments',[CommentController::class, 'index']);
    Route::put('/comments/{id}',[CommentController::class, 'update']);
    Route::delete('/comments/{id}',[CommentController::class, 'destroy']);

    // Admin Routes - Note: removed duplicate auth:sanctum and /api prefix
    Route::middleware('role:admin')->group(function () {
        // User Management Routes
        Route::get('/admin/users', [AdminController::class, 'index']);
        Route::post('/admin/users', [AdminController::class, 'store']);
        Route::put('/admin/users/{id}', [AdminController::class, 'update']);
        Route::delete('/admin/users/{id}', [AdminController::class, 'destroy']);
        
        // Statistics routes
        Route::get('/admin/statistics', [AdminController::class, 'getStatistics']);
    });
});

// Test route - remove /api prefix
Route::get('/test-role', function() {
    return response()->json(['message' => 'Role middleware is working']);
})->middleware(['auth:sanctum', 'role:admin']);

Route::post('/register',[UserController::class,'register']);
Route::post('/login',[UserController::class,'login']);