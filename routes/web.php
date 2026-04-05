<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RecipeController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/recipes'));
Route::get('/recipes', [RecipeController::class, 'index'])->name('recipes/index');
Route::get('/recipe/{recipe}', [RecipeController::class, 'show']);

Route::middleware('guest')->group(function () {
    //auth
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh']);

    //recipe
    Route::get('/recipes/create', [RecipeController::class, 'create'])->name('create');
    Route::post('/recipes/create', [RecipeController::class, 'store']);
    Route::get('/recipes/{recipe}/edit', [RecipeController::class, 'edit']);
    Route::post('/recipes/{recipe}/update', [RecipeController::class, 'update']);
    Route::delete('/recipes/{recipe}', [RecipeController::class, 'destroy']);

    //my-recipe
    Route::get('/my-recipes', [RecipeController::class, 'indexUser']);
});
