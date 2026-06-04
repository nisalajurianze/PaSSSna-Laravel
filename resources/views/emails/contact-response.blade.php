<!DOCTYPE html>
<html>
<head>
    <title>Contact Response</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #059669; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9fafb; }
        .message { background: white; padding: 15px; margin: 10px 0; border-left: 4px solid #059669; }
        .footer { text-align: center; padding: 20px; background: #f3f4f6; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📧 Response to Your Inquiry</h1>
            <p>PaSSSna Restaurant</p>
        </div>

        <div class="content">
            <p>Dear {{ $recipientName }},</p>
            <p>Thank you for contacting us. We have reviewed your inquiry and our response is below:</p>

            <div class="message">
                {!! nl2br(e($message)) !!}
            </div>

            <p>If you have any further questions or need additional assistance, please don't hesitate to contact us again.</p>

            <p>
                <a href="{{ url('/contact') }}" style="background: #059669; color: white; padding: 10px 20px; text-decoration: none; display: inline-block; border-radius: 5px;">
                    Contact Us Again
                </a>
            </p>
        </div>

        <div class="footer">
            <p>This is an automated response from PaSSSna Restaurant Management System.</p>
            <p>Generated on: {{ date('F d, Y') }}</p>
            <p>© {{ date('Y') }} PaSSSna Restaurant. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

