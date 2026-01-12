<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Leave Request Notification</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #4e73df;">New Leave Request</h2>
        
        <p>Hello {{ $manager->first_name }},</p>
        
        <p><strong>{{ $employee->full_name }}</strong> has submitted a leave request that requires your approval.</p>
        
        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p><strong>Leave Details:</strong></p>
            <ul style="list-style: none; padding: 0;">
                <li><strong>Employee:</strong> {{ $employee->full_name }} ({{ $employee->employee_code }})</li>
                <li><strong>Leave Type:</strong> {{ $leaveRequest->leaveType->name }}</li>
                <li><strong>Start Date:</strong> {{ $leaveRequest->start_date->format('M d, Y') }}</li>
                <li><strong>End Date:</strong> {{ $leaveRequest->end_date->format('M d, Y') }}</li>
                <li><strong>Total Days:</strong> {{ $leaveRequest->total_days }}</li>
                <li><strong>Reason:</strong> {{ $leaveRequest->reason }}</li>
            </ul>
        </div>
        
        <p>
            <a href="{{ route('hr.leaves.show', $leaveRequest) }}" 
               style="display: inline-block; padding: 10px 20px; background-color: #4e73df; color: white; text-decoration: none; border-radius: 5px;">
                Review Leave Request
            </a>
        </p>
        
        <p style="margin-top: 30px; color: #666; font-size: 12px;">
            This is an automated notification from the HR Management System.
        </p>
    </div>
</body>
</html>
