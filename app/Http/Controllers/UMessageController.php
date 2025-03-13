<?php

namespace App\Http\Controllers;

use App\Models\UMessage;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class UMessageController extends Controller
{
    /**
     * Get conversation between two employees
     */
    public function getConversation($employeeId, $otherEmployeeId)
    {
        try {
            // Get messages where employee1 is sender and employee2 is receiver
            // OR employee1 is receiver and employee2 is sender
            $messages = UMessage::where(function ($query) use ($employeeId, $otherEmployeeId) {
                $query->where('sender_id', $employeeId)
                      ->where('receiver_id', $otherEmployeeId);
            })
            ->orWhere(function ($query) use ($employeeId, $otherEmployeeId) {
                $query->where('sender_id', $otherEmployeeId)
                      ->where('receiver_id', $employeeId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

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
     * Send a new message
     */
    public function sendMessage(Request $request)
    {
        try {
            $request->validate([
                'sender_id' => 'required|exists:employees,id',
                'receiver_id' => 'required|exists:employees,id',
                'message' => 'required|string',
            ]);

            $message = UMessage::create([
                'sender_id' => $request->sender_id,
                'receiver_id' => $request->receiver_id,
                'message' => $request->message,
            ]);

            return response()->json([
                'status' => 'success',
                'data' => $message
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get the latest conversations for an employee
     */
    public function getLatestConversations($employeeId)
    {
        try {
            // Get the latest message from each conversation the employee is part of
            $latestMessages = DB::select("
                SELECT m.*
                FROM umessages m
                INNER JOIN (
                    SELECT 
                        CASE 
                            WHEN sender_id = ? THEN receiver_id 
                            ELSE sender_id 
                        END as other_employee_id,
                        MAX(created_at) as latest_date
                    FROM umessages
                    WHERE sender_id = ? OR receiver_id = ?
                    GROUP BY other_employee_id
                ) latest ON (
                    (m.sender_id = ? AND m.receiver_id = latest.other_employee_id) OR 
                    (m.sender_id = latest.other_employee_id AND m.receiver_id = ?)
                ) AND m.created_at = latest.latest_date
                ORDER BY m.created_at DESC
            ", [$employeeId, $employeeId, $employeeId, $employeeId, $employeeId]);

            return response()->json([
                'status' => 'success',
                'data' => $latestMessages
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch conversations',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}