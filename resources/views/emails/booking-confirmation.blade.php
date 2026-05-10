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
            background-color: #f4f4;
        }
        .email-container {
        background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
          padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0 0 10px 0;
        font-size: 28px;
      }
        .header p {
            margin: 0;
            font-size: 16px;
            opacity: 0.95;
        }
        .content {
          padding: 30px;
        }
        .success-badge {
        background: #d4edda;
            border: 2px solid #28a745;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
        text-align: center;
            margin: 20px 0;
            font-weight: 600;
        }
        .info-box {
            background: #f8f9fa;
            padding: 20px;
         border-radius: 8px;
          margin: 20px 0;
            border-left: 4px solid #28a745;
        }
     .info-box h3 {
            margin-top: 0;
            color: #28a745;
            font-size: 18px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #dee2e6;
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
            text-align: right;
     }
        .highlight {
            background: #fff3cd;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #ffc107;
        }
      .payment-summary {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .payment-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
       font-size: 16px;
        }
        .payment-row.total {
            border-top: 2px solid #28a745;
            margin-top: 10px;
            padding-top: 15px;
          font-weight: 700;
            font-size: 20px;
            color: #28a745;
        }
        .btn {
          display: inline-block;
          padding: 14px 35px;
          background: #28a745;
       color: white !important;
            text-decoration: none;
         border-radius: 6px;
            font-weight: 600;
       margin: 20px 0;
            text-align: center;
        }
        .btn:hover {
          background: #218838;
        }
        .calendar-note {
            background: #e7f3ff;
            border-left: 4px solid #007bff;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
      }
        .calendar-note strong {
         color: #007bff;
        }
        .footer {
            background: #f8f9fa;
            padding: 30px;
         text-align: center;
            border-top: 1px solid #dee2e6;
        }
        .footer p {
            margin: 5px 0;
          color: #666;
            font-size: 14px;
      }
        .contact-info {
            background: #fff;
            border: 1px solid #dee2e6;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        @media only screen and (max-width: 600px) {
            body {
              padding: 10px;
            }
            .content {
                padding: 20px;
            }
        .header {
                padding: 30px 20px;
            }
            .info-row, .payment-row {
                flex-direction: column;
            }
        .value {
          text-align: left;
         margin-top: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>✅ Booking Confirmed!</h1>
            <p>Your booth reservation has been successfully confirmed</p>
        </div>
        
        <div class="content">
            <p>Dear <strong>{{ $client->name ?? 'Valued Customer' }}</strong>,</p>
            
            <div class="success-badge">
                🎉 Congratulations! Your booking has been confirmed.
            </div>
            
            <p>Thank you for choosing KHB Events! We're excited to have you at our upcoming event. Below are the details of your confirmed booking:</p>
            
            <!-- Booking Details -->
            <div class="info-box">
                <h3>📋 Booking Information</h3>
            <div class="info-row">
               <span class="label">Booking ID:</span>
         <span class="value"><strong>#{{ $booking->id }}</strong></span>
          </div>
          <div class="info-row">
                 <span class="label">Booking Date:</span>
                  <span class="value">{{ $bookingDate ? $bookingDate->format('F d, Y') : 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Status:</span>
                  <span class="value"><strong style="color: #28a745;">{{ $status }}</strong></span>
              </div>
            </div>
            
            <!-- Booth Details -->
          @if($booth)
            <div class="info-box">
                <h3>🏢 Booth Details</h3>
                <div class="info-row">
                    <span class="label">Booth Number:</span>
                    <span class="value"><strong style="font-size: 18px; color: #28a745;">{{ $booth->booth_number }}</strong></span>
                </div>
                @if($booth->size)
              <div class="info-row">
                  <span class="label">Booth Size:</span>
               <span class="value">{{ $booth->size }}</span>
             </div>
         @endif
                @if($booth->zone)
       <div class="info-row">
                <span class="label">Zone:</span>
                  <span class="value">{{ $booth->zone }}</span>
                </div>
                @endif
            </div>
            @endif
            
            <!-- Event Details -->
            @if($event)
         <div class="info-box">
          <h3>📅 Event Details</h3>
                <div class="info-row">
         <span class="label">Event Name:</span>
                 <span class="value"><strong>{{ $event->name }}</strong></span>
                </div>
                @if($event->start_date)
                <div class="info-row">
                    <span class="label">Start Date:</span>
                 <span class="value">{{ \Carbon\Carbon::parse($event->start_date)->format('F d, Y') }}</span>
                </div>
                @endif
                @if($event->end_date)
                <div class="info-row">
                  <span class="label">End Date:</span>
                    <span class="value">{{ \Carbon\Carbon::parse($event->end_date)->format('F d, Y') }}</span>
              </div>
                @endif
                @if($event->location)
                <div class="info-row">
               <span class="label">Location:</span>
               <span class="value">{{ $event->location }}</span>
                </div>
                @endif
            </div>
            @endif
            
            <!-- Payment Summary -->
            @if($totalAmount > 0)
            <div class="payment-summary">
            <h3 style="margin-top: 0; color: #333;">💰 Payment Summary</h3>
                <div class="payment-row">
                    <span>Total Amount:</span>
               <span>${{ number_format($totalAmount, 2) }}</span>
                </div>
                @if($paidAmount > 0)
             <div class="payment-row">
                    <span>Amount Paid:</span>
                    <span style="color: #28a745;">${{ number_format($paidAmount, 2) }}</span>
                </div>
             @endif
                @if($balanceAmount > 0)
                <div class="payment-row total">
                  <span>Balance Due:</span>
                    <span>${{ number_format($balanceAmount, 2) }}</span>
              </div>
                @if($paymentDueDate)
                <div style="text-align: center; margin-top: 10px; color: #dc3545; font-weight: 600;">
              Due Date: {{ \Carbon\Carbon::parse($paymentDueDate)->format('F d, Y') }}
             </div>
                @endif
             @endif
            </div>
            @endif
          
            <!-- Calendar Attachment Note -->
          @if($event && $event->start_date)
            <div class="calendar-note">
                <strong>📆 Calendar Reminder:</strong> We've attached a calendar file (.ics) to this email. 
                Click on it to add this event to your calendar (Outlook, Google Calendar, Apple Calendar, etc.)
            </div>
            @endif
            
         <!-- Next Steps -->
            <div class="highlight">
        <h4 style="margin-top: 0;">📝 Next Steps:</h4>
                <ol style="margin: 10px 0; padding-left: 20px;">
             @if($balanceAmount > 0)
                    <li>Complete your payment by {{ $paymentDueDate ? \Carbon\Carbon::parse($paymentDueDate)->format('F d, Y') : 'the due date' }}</li>
                    @endif
           <li>Add the event to your calendar using the attached .ics file</li>
          <li>Prepare your booth materials and setup requirements</li>
              <li>Contact us if you have any questions or special requests</li>
        </ol>
            </div>
        
            <!-- Action Button -->
            <center>
                <a href="{{ config('app.url') }}/books/{{ $booking->id }}" class="btn">View Booking Details</a>
            </center>
          
            <!-- Contact Information -->
            <div class="contact-info">
             <h4 style="margin-top: 0; color: #333;">📞 Need Help?</h4>
                <p style="margin: 5px 0;">If you have any questions or need assistance, please don't hesitate to contact us:</p>
                <p style="margin: 5px 0;"><strong>Email:</strong> support@khbevents.com</p>
             <p style="margin: 5px 0;"><strong>Phone:</strong> +855 XX XXX XXX</p>
             <p style="margin: 5px 0;"><strong>Website:</strong> {{ config('app.url') }}</p>
            </div>
            
            <p style="margin-top: 30px;">We look forward to seeing you at the event!</p>
        
            <p>Best regards,<br>
            <strong>The KHB Events Team</strong><br>
            {{ config('app.name') }}</p>
        </div>
        
        <div class="footer">
            <p><strong>{{ config('app.name') }}</strong></p>
         <p>This is an automated confirmation email.</p>
            <p>&copy; {{ date('Y') }} KHB Events. All rights reserved.</p>
      </div>
    </div>
</body>
</html>
