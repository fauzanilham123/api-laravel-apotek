<?php

use Illuminate\Http\Request;
use App\Http\Middleware\Admin;
use App\Http\Middleware\Cashier;
use App\Http\Middleware\pharmacist;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DrugController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\TransactionController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
// Route GET tanpa middleware
Route::get('/drugs', [DrugController::class, 'index']);
Route::get('/drugs/{drug}', [DrugController::class, 'show']);
Route::get('/recipes', [RecipeController::class, 'index']);
Route::get('/recipes/{recipe}', [RecipeController::class, 'show']);
Route::get('/user', [UserController::class, 'index']);
Route::get('/user/{user}', [UserController::class, 'show']);
Route::get('/cashier', [CashierController::class, 'index']);
Route::get('/transaction', [TransactionController::class, 'index']);


Route::post('/cashier', [CashierController::class, 'store'])->middleware(['auth:api', Cashier::class]);
Route::apiResource('/drugs', DrugController::class)->except(['index', 'show'])->middleware(['auth:api', Admin::class]);

Route::apiResource('/recipes', RecipeController::class)->except(['index', 'show'])->middleware(['auth:api', Pharmacist::class]);;