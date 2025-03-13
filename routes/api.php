<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\MessageController;

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

// Employee routes
Route::get('/employees', [EmployeeController::class, 'index']);  // Get all employees
Route::get('/employee/{id}', [EmployeeController::class, 'show']);  // Get single employee by ID
Route::post('/employee/add', [EmployeeController::class, 'store']);
Route::put('/employee/{id}', [EmployeeController::class, 'update']);
Route::delete('/employee/{id}', [EmployeeController::class, 'destroy']);

// Message routes
Route::get('/messages', [MessageController::class, 'index']);  // Get all messages
Route::get('/messages/{id}', [MessageController::class, 'show']);  // Get specific message
Route::post('/messages/add', [MessageController::class, 'store']);  // Create a new message
Route::put('/messages/{id}', [MessageController::class, 'update']);  // Update a message
Route::delete('/messages/{id}', [MessageController::class, 'destroy']);  // Delete a message
Route::get('/employee/{employeeId}/messages', [MessageController::class, 'employeeMessages']);  // Get all messages for a specific employee