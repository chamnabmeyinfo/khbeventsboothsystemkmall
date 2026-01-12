<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\HR\LeaveRequest;
use App\Models\HR\Attendance;
use App\Models\HR\EmployeeDocument;
use App\Models\HR\Employee;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class HRNotificationService
{
    /**
     * Send leave request notification to manager
     */
    public function notifyLeaveRequestSubmitted(LeaveRequest $leaveRequest)
    {
        $employee = $leaveRequest->employee;
        $manager = $employee->manager;

        if (!$manager || !$manager->user) {
            return;
        }

        $title = 'New Leave Request';
        $message = "{$employee->full_name} has submitted a leave request for {$leaveRequest->total_days} day(s) from {$leaveRequest->start_date->format('M d')} to {$leaveRequest->end_date->format('M d, Y')}.";
        $link = route('hr.leaves.show', $leaveRequest);

        // Create in-app notification
        Notification::create([
            'type' => 'hr.leave_request',
            'title' => $title,
            'message' => $message,
            'user_id' => $manager->user_id,
            'link' => $link,
        ]);

        // Send email notification
        try {
            if ($manager->email) {
                Mail::send('emails.hr.leave-request', [
                    'leaveRequest' => $leaveRequest,
                    'employee' => $employee,
                    'manager' => $manager,
                ], function ($mail) use ($manager, $title) {
                    $mail->to($manager->email)
                        ->subject($title);
                });
            }
        } catch (\Exception $e) {
            Log::error('Failed to send leave request email: ' . $e->getMessage());
        }
    }

    /**
     * Notify employee when leave is approved
     */
    public function notifyLeaveApproved(LeaveRequest $leaveRequest)
    {
        $employee = $leaveRequest->employee;

        if (!$employee || !$employee->user) {
            return;
        }

        $title = 'Leave Request Approved';
        $message = "Your leave request for {$leaveRequest->total_days} day(s) from {$leaveRequest->start_date->format('M d')} to {$leaveRequest->end_date->format('M d, Y')} has been approved.";
        $link = route('employee.leaves');

        Notification::create([
            'type' => 'hr.leave_approved',
            'title' => $title,
            'message' => $message,
            'user_id' => $employee->user_id,
            'link' => $link,
        ]);

        // Send email
        try {
            if ($employee->email) {
                Mail::send('emails.hr.leave-approved', [
                    'leaveRequest' => $leaveRequest,
                    'employee' => $employee,
                ], function ($mail) use ($employee, $title) {
                    $mail->to($employee->email)
                        ->subject($title);
                });
            }
        } catch (\Exception $e) {
            Log::error('Failed to send leave approved email: ' . $e->getMessage());
        }
    }

    /**
     * Notify employee when leave is rejected
     */
    public function notifyLeaveRejected(LeaveRequest $leaveRequest)
    {
        $employee = $leaveRequest->employee;

        if (!$employee || !$employee->user) {
            return;
        }

        $title = 'Leave Request Rejected';
        $message = "Your leave request for {$leaveRequest->total_days} day(s) from {$leaveRequest->start_date->format('M d')} to {$leaveRequest->end_date->format('M d, Y')} has been rejected.";
        if ($leaveRequest->rejection_reason) {
            $message .= " Reason: {$leaveRequest->rejection_reason}";
        }
        $link = route('employee.leaves');

        Notification::create([
            'type' => 'hr.leave_rejected',
            'title' => $title,
            'message' => $message,
            'user_id' => $employee->user_id,
            'link' => $link,
        ]);

        // Send email
        try {
            if ($employee->email) {
                Mail::send('emails.hr.leave-rejected', [
                    'leaveRequest' => $leaveRequest,
                    'employee' => $employee,
                ], function ($mail) use ($employee, $title) {
                    $mail->to($employee->email)
                        ->subject($title);
                });
            }
        } catch (\Exception $e) {
            Log::error('Failed to send leave rejected email: ' . $e->getMessage());
        }
    }

    /**
     * Notify manager when attendance needs approval
     */
    public function notifyAttendancePending(Attendance $attendance)
    {
        $employee = $attendance->employee;
        $manager = $employee->manager;

        if (!$manager || !$manager->user) {
            return;
        }

        $title = 'Attendance Approval Required';
        $message = "{$employee->full_name}'s attendance for {$attendance->date->format('M d, Y')} requires your approval.";
        $link = route('hr.attendance.show', $attendance);

        Notification::create([
            'type' => 'hr.attendance_pending',
            'title' => $title,
            'message' => $message,
            'user_id' => $manager->user_id,
            'link' => $link,
        ]);
    }

    /**
     * Notify employee when attendance is approved
     */
    public function notifyAttendanceApproved(Attendance $attendance)
    {
        $employee = $attendance->employee;

        if (!$employee || !$employee->user) {
            return;
        }

        $title = 'Attendance Approved';
        $message = "Your attendance for {$attendance->date->format('M d, Y')} has been approved.";
        $link = route('employee.attendance');

        Notification::create([
            'type' => 'hr.attendance_approved',
            'title' => $title,
            'message' => $message,
            'user_id' => $employee->user_id,
            'link' => $link,
        ]);
    }

    /**
     * Notify employee about document expiry
     */
    public function notifyDocumentExpiring(EmployeeDocument $document, $daysUntilExpiry)
    {
        $employee = $document->employee;

        if (!$employee || !$employee->user) {
            return;
        }

        $title = 'Document Expiring Soon';
        $message = "Your document '{$document->document_name}' will expire in {$daysUntilExpiry} day(s) on {$document->expiry_date->format('M d, Y')}.";
        $link = route('employee.documents');

        Notification::create([
            'type' => 'hr.document_expiring',
            'title' => $title,
            'message' => $message,
            'user_id' => $employee->user_id,
            'link' => $link,
        ]);

        // Send email for urgent expiry (7 days or less)
        if ($daysUntilExpiry <= 7) {
            try {
                if ($employee->email) {
                    Mail::send('emails.hr.document-expiring', [
                        'document' => $document,
                        'employee' => $employee,
                        'daysUntilExpiry' => $daysUntilExpiry,
                    ], function ($mail) use ($employee, $title) {
                        $mail->to($employee->email)
                            ->subject($title);
                    });
                }
            } catch (\Exception $e) {
                Log::error('Failed to send document expiry email: ' . $e->getMessage());
            }
        }
    }

    /**
     * Notify employee about upcoming performance review
     */
    public function notifyPerformanceReviewUpcoming($employee, $reviewDate)
    {
        if (!$employee || !$employee->user) {
            return;
        }

        $title = 'Upcoming Performance Review';
        $message = "You have a performance review scheduled for {$reviewDate->format('M d, Y')}.";
        $link = route('hr.performance.index');

        Notification::create([
            'type' => 'hr.performance_review',
            'title' => $title,
            'message' => $message,
            'user_id' => $employee->user_id,
            'link' => $link,
        ]);
    }

    /**
     * Notify employee about birthday
     */
    public function notifyBirthday(Employee $employee)
    {
        if (!$employee || !$employee->user) {
            return;
        }

        $title = 'Happy Birthday!';
        $message = "Wishing you a wonderful birthday, {$employee->first_name}! 🎉";
        $link = route('employee.dashboard');

        Notification::create([
            'type' => 'hr.birthday',
            'title' => $title,
            'message' => $message,
            'user_id' => $employee->user_id,
            'link' => $link,
        ]);
    }
}
