<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Employee;
use App\Models\Message;
use Illuminate\Http\Request;
use Exception;

class NotificationController extends Controller
{
    /**
     * Get notifications for a specific employee
     */
    public function getEmployeeNotifications($employeeId)
    {
        try {
            $notifications = Notification::where('employee_id', $employeeId)
                ->orderBy('created_at', 'desc')
                ->take(20) // Limit to recent 20 notifications
                ->get();
            
            return response()->json([
                'status' => 'success',
                'data' => $notifications
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get new notifications for a specific employee since a given time
     */
    public function getNewNotifications(Request $request, $employeeId)
    {
        try {
            $since = $request->query('since');
            
            if (!$since) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Missing since parameter'
                ], 400);
            }
            
            $notifications = Notification::where('employee_id', $employeeId)
                ->where('created_at', '>', $since)
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json([
                'status' => 'success',
                'data' => $notifications
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch new notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark notifications as read
     */
    public function markAsRead(Request $request)
    {
        try {
            $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'notification_ids' => 'array',
                'notification_ids.*' => 'exists:notifications,id',
                'all' => 'boolean'
            ]);
            
            if ($request->all) {
                // Mark all notifications for the employee as read
                Notification::where('employee_id', $request->employee_id)
                    ->update(['read' => true]);
            } elseif ($request->notification_ids) {
                // Mark specific notifications as read
                Notification::whereIn('id', $request->notification_ids)
                    ->where('employee_id', $request->employee_id)
                    ->update(['read' => true]);
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Notifications marked as read'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to mark notifications as read',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new post notification for all employees except the author
     */
    public function createPostNotification(Message $message)
    {
        try {
            $author = Employee::findOrFail($message->employee_id);
            $employees = Employee::where('id', '!=', $author->id)->get();
            
            foreach ($employees as $employee) {
                Notification::create([
                    'employee_id' => $employee->id,
                    'title' => 'New Post',
                    'message' => "{$author->first_name} {$author->last_name} posted \"{$message->title}\"",
                    'type' => 'post',
                    'reference_id' => $message->id,
                    'read' => false
                ]);
            }
            
            return true;
        } catch (Exception $e) {
            // Log the error but don't stop the post creation process
            \Log::error('Failed to create post notification: ' . $e->getMessage());
            return false;
        }
    }
}