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
            background-color: #f4f4f4;
        }
        .email-container {
            background: white;
            border-radius: 8px;
          overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0.1);
      }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
          padding: 50px 30px;
            text-align: center;
        }
        .header h1 {
          margin: 0 0 15px 0;
            font-size: 32px;
        }
      .header p {
            margin: 0;
            font-size: 18px;
            opacity: 0.95;
        }
        .content {
            padding: 40px 30px;
        }
        .welcome-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
         padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin: 25px 0;
            font-size: 18px;
            font-weight: 600;
        }
        .feature-grid {
          display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 30px 0;
        }
        .feature-box {
            background: #f8f9fa;
            padding: 20px;
          border-radius: 8px;
            text-align: center;
            border: 2px solid #e9ecef;
       transition: all 0.3s;
        }
        .feature-box:hover {
            border-color: #667eea;
            transform: translateY(-2px);
        }
        .feature-icon {
            font-size: 36px;
      margin-bottom: 10px;
        }
        .feature-title {
        font-weight: 600;
       color: #667eea;
        margin: 10px 0 5px 0;
        }
        .feature-desc {
            font-size: 14px;
        color: #666;
        }
        .info-box {
            background: #e7f3ff;
          border-left: 4px solid #007bff;
            padding: 20px;
            border-radius: 4px;
        margin: 25px 0;
        }
        .info-box h3 {
         margin-top: 0;
            color: #007bff;
        }
     .btn {
            display: inline-block;
        padding: 16px 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
         text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
         margin: 20px 0;
        font-size: 16px;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
     .steps {
            background: #f8f9fa;
         padding: 25px;
            border-radius: 8px;
            margin: 25px 0;
        }
        .step {
            display: flex;
            align-items: flex-start;
         margin: 15px 0;
        }
        .step-number {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
         display: flex;
            align-items: center;
          justify-content: center;
            font-weight: 700;
          flex-shrink: 0;
          margin-right: 15px;
        }
        .step-content {
       flex: 1;
        }
        .step-title {
            font-weight: 600;
            margin-bottom: 5px;
        }
        .contact-box {
            background: #fff;
            border: 2px solid #667eea;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
        }
        .contact-box h4 {
            margin-top: 0;
         color: #667eea;
     }
        .contact-item {
         margin: 10px 0;
            display: flex;
          align-items: center;
        }
        .contact-icon {
            margin-right: 10px;
            font-size: 18px;
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
        .social-links {
         margin: 20px 0;
        }
        .social-links a {
        display: inline-block;
            margin: 0 10px;
            color: #667eea;
            text-decoration: none;
          font-size: 24px;
        }
        @media only screen and (max-width: 600px) {
         body {
            padding: 10px;
            }
          .content {
                padding: 25px 20px;
            }
            .header {
                padding: 40px 20px;
            }
            .feature-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🎉 Welcome to KHB Events!</h1>
            <p>We're thrilled to have you join us</p>
        </div>
        
        <div class="content">
            <p style="font-size: 18px;">Dear <strong>{{ $clientName }}</strong>,</p>
            
        <div class="welcome-badge">
                Welcome to the KHB Events family! 🎊
          </div>
          
          <p>Thank you for choosing KHB Events as your event partner. We're excited to help you create memorable experiences and successful events!</p>
            
            @if($company)
      <p>We're honored to work with <strong>{{ $company }}</strong> and look forward to a long and successful partnership.</p>
            @endif
      
          <!-- What You Can Do -->
            <h2 style="color: #667eea; margin-top: 35px;">🚀 What You Can Do</h2>
            
            <div class="feature-grid">
                <div class="feature-box">
                    <div class="feature-icon">🏢</div>
               <div class="feature-title">Book Booths</div>
              <div class="feature-desc">Reserve your perfect booth space for upcoming events</div>
          </div>
              <div class="feature-box">
                    <div class="feature-icon">📅</div>
                    <div class="feature-title">Manage Events</div>
               <div class="feature-desc">View and manage all your event bookings in one place</div>
             </div>
                <div class="feature-box">
                  <div class="feature-icon">💳</div>
              <div class="feature-title">Track Payments</div>
          <div class="feature-desc">Monitor payment status and receive instant receipts</div>
             </div>
                <div class="feature-box">
                    <div class="feature-icon">📊</div>
                    <div class="feature-title">View Reports</div>
                    <div class="feature-desc">Access detailed reports and booking history</div>
             </div>
            </div>
       
            <!-- Getting Started -->
      <h2 style="color: #667eea; margin-top: 35px;">📝 Getting Started</h2>
         
       <div class="steps">
                <div class="step">
               <div class="step-number">1</div>
                    <div class="step-content">
                        <div class="step-title">Explore Available Events</div>
              <div>Browse our upcoming events and find the perfect opportunity for your business.</div>
                 </div>
                </div>
             <div class="step">
                    <div class="step-number">2</div>
                  <div class="step-content">
                        <div class="step-title">Select Your Booth</div>
                  <div>Choose from our interactive floor plans and select the booth that fits your needs.</div>
                 </div>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
              <div class="step-content">
            <div class="step-title">Complete Your Booking</div>
               <div>Finalize your reservation and receive instant confirmation.</div>
                    </div>
                </div>
              <div class="step">
                    <div class="step-number">4</div>
            <div class="step-content">
              <div class="step-title">Prepare for Success</div>
                   <div>Get ready for your event with our support and resources.</div>
            </div>
                </div>
            </div>
            
      <!-- Action Button -->
            <center>
              <a href="{{ config('app.url') }}" class="btn">Visit Your Dashboard</a>
            </center>
            
            <!-- Important Information -->
            <div class="info-box">
         <h3>💡 Important Information</h3>
                <ul style="margin: 10px 0; padding-left: 20px;">
                <li>You'll receive email confirmations for all bookings and payments</li>
                    <li>Calendar invites (.ics files) will be attached to booking confirmations</li>
                    <li>Payment reminders will be sent automatically before due dates</li>
           <li>Our team is available to assist you at any time</li>
         </ul>
            </div>
            
            <!-- Contact Information -->
            <div class="contact-box">
            <h4>📞 Need Help? We're Here for You!</h4>
                <p>Our dedicated support team is ready to assist you with any questions or concerns.</p>
              <div class="contact-item">
                    <span class="contact-icon">📧</span>
                    <span><strong>Email:</strong> support@khbevents.com</span>
            </div>
                <div class="contact-item">
                 <span class="contact-icon">📱</span>
            <span><strong>Phone:</strong> +855 XX XXX XXX</span>
                </div>
                <div class="contact-item">
                    <span class="contact-icon">🌐</span>
         <span><strong>Website:</strong> {{ config('app.url') }}</span>
              </div>
              <div class="contact-item">
                  <span class="contact-icon">⏰</span>
                    <span><strong>Hours:</strong> Monday - Friday, 9:00 AM - 6:00 PM ICT</span>
           </div>
      </div>
        
         <p style="margin-top: 30px;">We're committed to making your event experience exceptional. Don't hesitate to reach out if you need anything!</p>
            
        <p>Welcome aboard, and here's to many successful events together! 🎉</p>
      
       <p>Warm regards,<br>
            <strong>The KHB Events Team</strong><br>
            {{ config('app.name') }}</p>
        </div>
        
        <div class="footer">
            <p><strong>{{ config('app.name') }}</strong></p>
          <div class="social-links">
                <a href="#" title="Facebook">📘</a>
            <a href="#" title="Instagram">📷</a>
                <a href="#" title="LinkedIn">💼</a>
            </div>
            <p>This is an automated welcome email.</p>
        <p>&copy; {{ date('Y') }} KHB Events. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
