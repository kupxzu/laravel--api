<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\UMessageController;
use App\Http\Controllers\NotificationController;

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




// Add these routes to your routes/api.php file

// Get conversation between two employees
Route::get('/umessages/{employeeId}/{otherEmployeeId}', [UMessageController::class, 'getConversation']);

// Send a new message
Route::post('/umessages/send', [UMessageController::class, 'sendMessage']);

// Get latest conversations for an employee
Route::get('/umessages/latest/{employeeId}', [UMessageController::class, 'getLatestConversations']);


Route::get('/notifications/{employeeId}', [NotificationController::class, 'getEmployeeNotifications']);

// Get new notifications for an employee since a given time
Route::get('/notifications/new/{employeeId}', [NotificationController::class, 'getNewNotifications']);

// Mark notifications as read
Route::post('/notifications/mark-read', [NotificationController::class, 'markAsRead']);