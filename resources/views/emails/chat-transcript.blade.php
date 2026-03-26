<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Transcript - {{ $bot->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: #f5f7fa;
            padding: 40px 20px;
        }
        .container {
            max-width: 680px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            text-align: center;
        }
        .logo {
            width: 56px;
            height: 56px;
            background: rgba(255,255,255,0.2);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }
        h1 {
            color: white;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .meta {
            background: #f8fafc;
            padding: 20px 30px;
            border-bottom: 1px solid #e2e8f0;
        }
        .meta-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .meta-label {
            color: #64748b;
            font-weight: 500;
        }
        .meta-value {
            color: #0f172a;
            font-weight: 600;
        }
        .messages {
            padding: 30px;
        }
        .message {
            margin-bottom: 20px;
            display: flex;
        }
        .message.user {
            justify-content: flex-end;
        }
        .message.bot, .message.admin {
            justify-content: flex-start;
        }
        .bubble {
            max-width: 80%;
            padding: 12px 16px;
            border-radius: 16px;
            position: relative;
        }
        .message.user .bubble {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom-right-radius: 4px;
        }
        .message.bot .bubble {
            background: #f1f5f9;
            color: #0f172a;
            border-bottom-left-radius: 4px;
        }
        .message.admin .bubble {
            background: #dcfce7;
            color: #166534;
            border-bottom-left-radius: 4px;
        }
        .meta-time {
            font-size: 10px;
            margin-top: 6px;
            opacity: 0.7;
        }
        .footer {
            background: #f8fafc;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            padding: 10px 24px;
            border-radius: 40px;
            font-size: 14px;
            font-weight: 600;
            margin-top: 16px;
        }
        @media (max-width: 600px) {
            .container { border-radius: 16px; }
            .messages { padding: 20px; }
            .bubble { max-width: 90%; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="28" height="28" stroke="white">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
            <h1>Chat Transcript</h1>
            <p style="color: rgba(255,255,255,0.8); font-size: 14px;">{{ $bot->name }}</p>
        </div>

        <div class="meta">
            <div class="meta-item">
                <span class="meta-label">Session ID:</span>
                <span class="meta-value">{{ $session->session_id }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Started:</span>
                <span class="meta-value">{{ $session->start_time->format('F j, Y, g:i a') }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Ended:</span>
                <span class="meta-value">{{ $session->last_admin_time ? $session->last_admin_time->format('F j, Y, g:i a') : 'Inactive' }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Total Messages:</span>
                <span class="meta-value">{{ $messages->count() }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Admin Messages:</span>
                <span class="meta-value">{{ $session->admin_msg_count }}</span>
            </div>
        </div>

        <div class="messages">
            @foreach($messages as $msg)
                <div class="message {{ $msg->role }}">
                    <div class="bubble">
                        <div style="font-weight: 600; font-size: 12px; margin-bottom: 4px;">
                            {{ ucfirst($msg->role) }}
                        </div>
                        <div style="font-size: 14px; line-height: 1.5;">
                            {{ nl2br(e($msg->content)) }}
                        </div>
                        <div class="meta-time">
                            {{ $msg->created_at->format('g:i a') }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="footer">
            <p>This is an automated notification from SaaS AI Chatbot.</p>
            <p>To view the full conversation, click the button below:</p>
            <a href="{{ route('bots.history', $bot) }}?session_id={{ urlencode($session->session_id) }}" class="button">
                View in Dashboard
            </a>
            <p style="margin-top: 16px;">&copy; {{ date('Y') }} SaaS AI Chatbot. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
