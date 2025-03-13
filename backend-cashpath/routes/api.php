<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CategoryController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



// ✅ Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// ✅ Protected Routes (Require Authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
    Route::put('/update-profile', [AuthController::class, 'updateProfile']);
    Route::put('/update-password', [AuthController::class, 'updatePassword']);
    Route::post('/logout', [AuthController::class, 'logout']);
});


Route::middleware('auth:sanctum')->group(function () {
    // ✅ Accounts API
    Route::get('/accounts', [AccountController::class, 'index']);           // Get all accounts
    Route::post('/accounts', [AccountController::class, 'store']);          // Create an account
    Route::get('/accounts/{id}', [AccountController::class, 'show']);       // Get single account
    Route::put('/accounts/{id}', [AccountController::class, 'update']);     // Update an account
    Route::delete('/accounts/{id}', [AccountController::class, 'destroy']); // Delete an account
    Route::get('/accounts/balance/total', [AccountController::class, 'totalBalance']); // Get total balance
    Route::patch('/accounts/{id}/set-default', [AccountController::class, 'setDefault']); // Set default account
});


Route::middleware('auth:sanctum')->group(function () {
    // ✅ Transactions API
    // ✅ Transactions API
    Route::get('/transactions', [TransactionController::class, 'index']); // Get all transactions
    Route::post('/transactions', [TransactionController::class, 'store']); // Create a transaction
    Route::get('/transactions/{id}', [TransactionController::class, 'show']); // Get single transaction
    Route::put('/transactions/{id}', [TransactionController::class, 'update']); // Update transaction
    Route::delete('/transactions/{id}', [TransactionController::class, 'destroy']); // Delete transaction
    Route::get('/user/transactions/summary', [TransactionController::class, 'getIncomeAndExpenses']); // Get total income/expenses


    // ✅ Yearly Transactions
    Route::get('/transactions/year/{year}', [TransactionController::class, 'getYearlyTransactions']);

    // ✅ Monthly Transactions
    Route::get('/transactions/month/{year}/{month}', [TransactionController::class, 'getMonthlyTransactions']);

    // ✅ Daily Transactions
    Route::get('/transactions/day/{year}/{month}/{day}', [TransactionController::class, 'getDailyTransactions']);

    // ✅ Calendar Transactions
    Route::get('/transactions/calendar/{year}/{month}', [TransactionController::class, 'getCalendarTransactions']);
    // ✅ Transactions by Description
    Route::get('/transactions/search', [TransactionController::class, 'getTransactionsByDescription']);

});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/categories', [CategoryController::class, 'index']); // Get all categories
    Route::post('/categories', [CategoryController::class, 'store']); // Create category
    Route::get('/categories/{id}', [CategoryController::class, 'show']); // Get a category
    Route::put('/categories/{id}', [CategoryController::class, 'update']); // Update category
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']); // Delete category
    Route::get('/categories/{id}/subcategories', [CategoryController::class, 'subcategories']); // Get subcategories
});