<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Document Expiring Soon</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #ffc107;">⚠️ Document Expiring Soon</h2>
        
        <p>Hello {{ $employee->first_name }},</p>
        
        <p>This is a reminder that one of your documents will expire soon.</p>
        
        <div style="background-color: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #ffc107;">
            <p><strong>Document Details:</strong></p>
            <ul style="list-style: none; padding: 0;">
                <li><strong>Document Name:</strong> {{ $document->document_name }}</li>
                <li><strong>Document Type:</strong> {{ $document->document_type }}</li>
                <li><strong>Expiry Date:</strong> {{ $document->expiry_date->format('M d, Y') }}</li>
                <li><strong>Days Remaining:</strong> {{ $daysUntilExpiry }} day(s)</li>
            </ul>
        </div>
        
        <p>Please ensure you renew this document before it expires.</p>
        
        <p>
            <a href="{{ route('employee.documents') }}" 
               style="display: inline-block; padding: 10px 20px; background-color: #ffc107; color: #333; text-decoration: none; border-radius: 5px;">
                View Documents
            </a>
        </p>
        
        <p style="margin-top: 30px; color: #666; font-size: 12px;">
            This is an automated notification from the HR Management System.
        </p>
    </div>
</body>
</html>
