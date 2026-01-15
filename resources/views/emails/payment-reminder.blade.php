<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .header.overdue {
            background: linear-gradient(135deg, #e74a3b 0%, #c23321 100%);
        }
        .header.due_today {
            background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .info-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: 600;
            color: #666;
        }
        .value {
            color: #333;
        }
        .total-due {
            background: #fff3cd;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
            border: 2px solid #ffc107;
        }
        .total-due.overdue {
            background: #f8d7da;
            border-color: #dc3545;
        }
        .total-amount {
            font-size: 2em;
            font-weight: 700;
            color: #dc3545;
            margin: 10px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header {{ $reminderType }}">
        @if($reminderType === 'overdue')
            <h1>⚠️ OVERDUE PAYMENT</h1>
            <p>Your payment is past due</p>
        @elseif($reminderType === 'due_today')
            <h1>🔔 PAYMENT DUE TODAY</h1>
            <p>Your payment is due today</p>
        @else
            <h1>📅 Payment Reminder</h1>
            <p>Your payment is coming up soon</p>
        @endif
    </div>
    
    <div class="content">
        <p>Dear {{ $client->name }},</p>
        
        <p>This is a friendly reminder about your upcoming payment for <strong>Booth {{ $booth->booth_number }}</strong>.</p>
        
        <div class="info-box">
            <h3 style="margin-top: 0;">Booth Details</h3>
            <div class="info-row">
                <span class="label">Booth Number:</span>
                <span class="value"><strong>{{ $booth->booth_number }}</strong></span>
            </div>
            <div class="info-row">
                <span class="label">Company:</span>
                <span class="value">{{ $client->company }}</span>
            </div>
            @if($booth->floorPlan)
            <div class="info-row">
                <span class="label">Event:</span>
                <span class="value">{{ $booth->floorPlan->name }}</span>
            </div>
            @endif
        </div>
        
        <div class="info-box">
            <h3 style="margin-top: 0;">Payment Details</h3>
            <div class="info-row">
                <span class="label">Payment Due Date:</span>
                <span class="value"><strong>{{ $dueDate->format('F d, Y') }}</strong></span>
            </div>
            @if($depositAmount > 0)
            <div class="info-row">
                <span class="label">Deposit Amount:</span>
                <span class="value">${{ number_format($depositAmount, 2) }}</span>
            </div>
            <div class="info-row">
                <span class="label">Deposit Paid:</span>
                <span class="value" style="color: #28a745;">${{ number_format($depositPaid, 2) }}</span>
            </div>
            @endif
            @if($balanceDue > 0)
            <div class="info-row">
                <span class="label">Balance Due:</span>
                <span class="value">${{ number_format($balanceDue, 2) }}</span>
            </div>
            <div class="info-row">
                <span class="label">Balance Paid:</span>
                <span class="value" style="color: #28a745;">${{ number_format($balancePaid, 2) }}</span>
            </div>
            @endif
        </div>
        
        <div class="total-due {{ $reminderType === 'overdue' ? 'overdue' : '' }}">
            <p style="margin: 0; font-weight: 600;">Total Amount Due:</p>
            <div class="total-amount">${{ number_format($totalDue, 2) }}</div>
            @if($reminderType === 'overdue')
                <p style="margin: 0; color: #dc3545; font-weight: 600;">⚠️ OVERDUE</p>
            @elseif($reminderType === 'due_today')
                <p style="margin: 0; color: #856404; font-weight: 600;">🔔 DUE TODAY</p>
            @else
                <p style="margin: 0;">Due: {{ $dueDate->format('F d, Y') }}</p>
            @endif
        </div>
        
        @if($reminderType === 'overdue')
            <p style="color: #dc3545; font-weight: 600;">⚠️ Your payment is overdue. Please make payment as soon as possible to avoid any service interruptions.</p>
        @endif
        
        <p>Please ensure payment is received by the due date. If you have any questions or concerns, please don't hesitate to contact us.</p>
        
        <center>
            <a href="{{ config('app.url') }}" class="btn">Make Payment</a>
        </center>
        
        <p>Thank you for your business!</p>
        
        <p>Best regards,<br>
        <strong>{{ config('app.name') }}</strong></p>
    </div>
    
    <div class="footer">
        <p>This is an automated reminder. Please do not reply to this email.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>
</html>
