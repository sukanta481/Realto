<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Follow-up Reminder</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background: #f4f4f5; }
        .container { max-width: 600px; margin: 20px auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); color: white; padding: 24px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 600; }
        .content { padding: 32px 24px; }
        .greeting { font-size: 18px; margin-bottom: 16px; }
        .alert-box { background: #fef3c7; border: 1px solid #fcd34d; border-radius: 8px; padding: 16px; margin: 24px 0; }
        .alert-box h3 { margin: 0 0 8px; color: #92400e; font-size: 16px; }
        .details { background: #f9fafb; border-radius: 8px; padding: 20px; margin: 24px 0; }
        .details-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e5e7eb; }
        .details-row:last-child { border-bottom: none; }
        .details-label { color: #6b7280; font-size: 14px; }
        .details-value { color: #111827; font-weight: 500; }
        .cta-button { display: inline-block; background: #4f46e5; color: white; text-decoration: none; padding: 14px 28px; border-radius: 8px; font-weight: 600; margin-top: 16px; }
        .footer { background: #f9fafb; padding: 20px 24px; text-align: center; color: #6b7280; font-size: 12px; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⏰ Follow-up Reminder</h1>
        </div>
        
        <div class="content">
            <p class="greeting">Hello {{ $user->name }},</p>
            
            <div class="alert-box">
                <h3>You have an upcoming follow-up!</h3>
                <p style="margin: 0; color: #92400e;">This is a reminder for your scheduled follow-up.</p>
            </div>

            <div class="details">
                <div class="details-row">
                    <span class="details-label">Purpose</span>
                    <span class="details-value">{{ $followUp->purpose }}</span>
                </div>
                <div class="details-row">
                    <span class="details-label">Type</span>
                    <span class="details-value">{{ ucfirst($followUp->type ?? 'Call') }}</span>
                </div>
                <div class="details-row">
                    <span class="details-label">Date & Time</span>
                    <span class="details-value">{{ $followUp->scheduled_at->format('d M Y, h:i A') }}</span>
                </div>
                @if($followUp->lead)
                <div class="details-row">
                    <span class="details-label">Lead</span>
                    <span class="details-value">{{ $followUp->lead->name }} ({{ $followUp->lead->phone }})</span>
                </div>
                @endif
                @if($followUp->notes)
                <div class="details-row">
                    <span class="details-label">Notes</span>
                    <span class="details-value">{{ $followUp->notes }}</span>
                </div>
                @endif
            </div>

            <center>
                <a href="{{ config('app.url') }}/follow-ups" class="cta-button">View Follow-ups</a>
            </center>
        </div>

        <div class="footer">
            <p>This is an automated reminder from RealtoCRM.</p>
            <p>© {{ date('Y') }} RealtoCRM - Real Estate Made Simple</p>
        </div>
    </div>
</body>
</html>
