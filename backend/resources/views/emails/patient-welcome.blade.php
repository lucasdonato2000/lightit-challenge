<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Patient Registration System</title>
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
            background-color: #4F46E5;
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .info-box {
            background-color: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 6px;
            border-left: 4px solid #4F46E5;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #6b7280;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Welcome!</h1>
    </div>

    <div class="content">
        <h2>Hello, {{ $patient->full_name }}!</h2>

        <p>Thank you for registering with our Patient Registration System.</p>

        <p>Your registration has been successfully completed. Here are your details:</p>

        <div class="info-box">
            <p><strong>Full Name:</strong> {{ $patient->full_name }}</p>
            <p><strong>Email:</strong> {{ $patient->email }}</p>
            <p><strong>Phone:</strong> {{ $patient->full_phone_number }}</p>
            <p><strong>Registration Date:</strong> {{ $patient->created_at->format('F j, Y, g:i a') }}</p>
        </div>

        <p>If you have any questions or need assistance, please don't hesitate to contact us.</p>

        <p>Best regards,<br>Patient Registration Team</p>
    </div>

    <div class="footer">
        <p>This is an automated email. Please do not reply to this message.</p>
        <p>&copy; {{ date('Y') }} Patient Registration System. All rights reserved.</p>
    </div>
</body>
</html>
