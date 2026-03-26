<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Chat Transcript - {{ $bot->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: linear-gradient(135deg, #fef9e7 0%, #fff5e6 100%);
            padding: 40px 20px;
        }
        .container {
            max-width: 680px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 32px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
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
            color: #2c2418;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .meta {
            background: #fffbeb;
            padding: 20px 30px;
            border-bottom: 1px solid #fde68a;
        }
        .meta-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .meta-label {
            color: #b45309;
            font-weight: 500;
        }
        .meta-value {
            color: #2c2418;
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
            border-radius: 20px;
            position: relative;
        }
        .message.user .bubble {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: #2c2418;
            border-bottom-right-radius: 4px;
        }
        .message.bot .bubble {
            background: #fffbeb;
            color: #78350f;
            border-bottom-left-radius: 4px;
            border: 1px solid #fde68a;
        }
        .message.admin .bubble {
            background: #dcfce7;
            color: #166534;
            border-bottom-left-radius: 4px;
        }
        .role-label {
            font-size: 10px;
            font-weight: 600;
            margin-bottom: 4px;
            opacity: 0.7;
        }
        .message-content {
            font-size: 14px;
            line-height: 1.5;
        }
        .meta-time {
            font-size: 10px;
            margin-top: 6px;
            opacity: 0.6;
        }
        .footer {
            background: #fffbeb;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #b45309;
            border-top: 1px solid #fde68a;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: #2c2418;
            text-decoration: none;
            padding: 10px 24px;
            border-radius: 40px;
            font-size: 14px;
            font-weight: 600;
            margin-top: 16px;
        }
        @media (max-width: 600px) {
            .container { border-radius: 24px; }
            .messages { padding: 20px; }
            .bubble { max-width: 90%; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="28" height="28" stroke="#2c2418">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
            <h1>Your Chat Transcript 💬</h1>
            <p style="color: #2c2418; opacity: 0.9;">{{ $bot->name }}</p>
        </div>

        <div class="meta">
            <div class="meta-item">
                <span class="meta-label">Conversation ID:</span>
                <span class="meta-value">{{ substr($session->session_id, 0, 30) }}...</span>
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
                <span class="meta-label">Messages:</span>
                <span class="meta-value">{{ $messages->count() }}</span>
            </div>
        </div>

        <div class="messages">
            @foreach($messages as $msg)
                <div class="message {{ $msg->role }}">
                    <div class="bubble">
                        <div class="role-label">
                            {{ $msg->role === 'user' ? 'You' : ($msg->role === 'admin' ? '✨ Helper' : '🤖 AI Assistant') }}
                        </div>
                        <div class="message-content">
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
            <p>This is a sweet reminder from SaaS AI Chatbot 💝</p>
            <a href="{{ route('bots.history', $bot) }}?session_id={{ urlencode($session->session_id) }}" class="button">
                View Full Conversation
            </a>
            <p style="margin-top: 16px;">&copy; {{ date('Y') }} SaaS AI Chatbot. Spreading kindness, one chat at a time.</p>
        </div>
    </div>
</body>
</html>
