<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to the Family! 🎉</title>
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
            max-width: 560px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 32px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            padding: 40px 30px;
            text-align: center;
        }
        .logo {
            width: 70px;
            height: 70px;
            background: rgba(255,255,255,0.2);
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        .logo svg {
            width: 36px;
            height: 36px;
            color: #2c2418;
        }
        h1 {
            color: #2c2418;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .content {
            padding: 40px 30px;
            text-align: center;
        }
        .greeting {
            font-size: 18px;
            color: #2c2418;
            margin-bottom: 16px;
        }
        .message {
            color: #78350f;
            line-height: 1.6;
            margin-bottom: 32px;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: #2c2418;
            text-decoration: none;
            padding: 14px 36px;
            border-radius: 40px;
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 32px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(245, 158, 11, 0.3);
        }
        .divider {
            height: 1px;
            background: #fde68a;
            margin: 24px 0;
        }
        .fallback-link {
            font-size: 12px;
            color: #9ca3af;
            word-break: break-all;
            background: #fffbeb;
            padding: 12px;
            border-radius: 16px;
        }
        .fallback-link a {
            color: #f59e0b;
            text-decoration: none;
        }
        .footer {
            background: #fffbeb;
            padding: 24px 30px;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
        }
        @media (max-width: 600px) {
            .container { border-radius: 24px; }
            .header { padding: 30px 20px; }
            .content { padding: 30px 20px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
            <h1>Welcome to the Family! 🎉</h1>
            <p style="color: #2c2418; opacity: 0.9;">SaaS AI Chatbot</p>
        </div>

        <div class="content">
            <div class="greeting">
                Hi <strong>{{ $user->name }}</strong>! 👋
            </div>
            <div class="message">
                We're so excited to have you join our community! 🥳<br>
                Please verify your email address to start building amazing AI chatbots.
            </div>

            <a href="{{ $verificationUrl }}" class="button">
                ✨ Verify My Email ✨
            </a>

            <div class="divider"></div>

            <div class="fallback-link">
                <p style="margin-bottom: 8px;">Button not working? Copy this link:</p>
                <a href="{{ $verificationUrl }}">{{ $verificationUrl }}</a>
            </div>

            <div style="margin-top: 24px; padding: 12px; background: #fffbeb; border-radius: 16px;">
                <p style="font-size: 12px; color: #b45309;">
                    <strong>💝 This link expires in 24 hours</strong><br>
                    If you didn't create an account, you can safely ignore this email.
                </p>
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} SaaS AI Chatbot. Spreading kindness, one chat at a time. 💝</p>
            <p style="margin-top: 8px;">Sent with love to {{ $user->email }}</p>
        </div>
    </div>
</body>
</html>
