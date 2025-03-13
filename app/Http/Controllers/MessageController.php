<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Employee;
use Illuminate\Http\Request;
use Exception;
use App\Http\Controllers\NotificationController;

class MessageController extends Controller
{
    /**
     * Display a listing of the messages.
     */
    public function index()
    {
        try {
            $messages = Message::with('employee')->get();
            return response()->json([
                'status' => 'success',
                'data' => $messages
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch messages',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display messages for a specific employee.
     */
    public function employeeMessages($employeeId)
    {
        try {
            $employee = Employee::findOrFail($employeeId);
            $messages = $employee->messages;
            
            return response()->json([
                'status' => 'success',
                'data' => $messages
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch employee messages',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created message.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'title' => 'required|string|max:255',
                'description' => 'required|string',
            ]);

            $message = Message::create([
                'employee_id' => $request->employee_id,
                'title' => $request->title,
                'description' => $request->description,
            ]);

            // Create notifications for all other employees
            app(NotificationController::class)->createPostNotification($message);

            return response()->json([
                'status' => 'success',
                'data' => $message
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified message.
     */
    public function show($id)
    {
        try {
            $message = Message::with('employee')->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => $message
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Message not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified message.
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
            ]);

            $message = Message::findOrFail($id);
            
            // Optional: Check if the user has permission to update this message
            // For example, you might want to check if the message belongs to the employee making the request
            
            $message->title = $request->title;
            $message->description = $request->description;
            $message->save();

            return response()->json([
                'status' => 'success',
                'data' => $message
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified message.
     */
    public function destroy($id)
    {
        try {
            $message = Message::findOrFail($id);
            
            // Optional: Check if the user has permission to delete this message
            // For example, you might want to check if the message belongs to the employee making the request
            
            $message->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Message deleted successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete message',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}