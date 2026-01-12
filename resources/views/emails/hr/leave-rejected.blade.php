<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Leave Request Rejected</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #dc3545;">Leave Request Rejected</h2>
        
        <p>Hello {{ $employee->first_name }},</p>
        
        <p>Unfortunately, your leave request has been <strong style="color: #dc3545;">rejected</strong>.</p>
        
        <div style="background-color: #f8d7da; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #dc3545;">
            <p><strong>Leave Details:</strong></p>
            <ul style="list-style: none; padding: 0;">
                <li><strong>Leave Type:</strong> {{ $leaveRequest->leaveType->name }}</li>
                <li><strong>Start Date:</strong> {{ $leaveRequest->start_date->format('M d, Y') }}</li>
                <li><strong>End Date:</strong> {{ $leaveRequest->end_date->format('M d, Y') }}</li>
                <li><strong>Total Days:</strong> {{ $leaveRequest->total_days }}</li>
                @if($leaveRequest->rejection_reason)
                <li><strong>Reason:</strong> {{ $leaveRequest->rejection_reason }}</li>
                @endif
            </ul>
        </div>
        
        <p>
            <a href="{{ route('employee.leaves') }}" 
               style="display: inline-block; padding: 10px 20px; background-color: #dc3545; color: white; text-decoration: none; border-radius: 5px;">
                View Leave Details
            </a>
        </p>
        
        <p style="margin-top: 30px; color: #666; font-size: 12px;">
            This is an automated notification from the HR Management System.
        </p>
    </div>
</body>
</html>
