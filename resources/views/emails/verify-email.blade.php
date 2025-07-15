<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('auth.verify_email_title') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8fafc;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 16px;
            opacity: 0.9;
        }

        .content {
            padding: 40px 30px;
        }

        .welcome-message {
            text-align: center;
            margin-bottom: 30px;
        }

        .welcome-message h2 {
            color: #2d3748;
            font-size: 24px;
            margin-bottom: 15px;
        }

        .welcome-message p {
            font-size: 16px;
            color: #718096;
            line-height: 1.7;
        }

        .verification-section {
            background-color: #f7fafc;
            border-radius: 8px;
            padding: 30px;
            margin: 30px 0;
            text-align: center;
        }

        .verification-button {
            display: inline-block;
            padding: 15px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            transition: transform 0.2s ease;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .verification-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
        }

        .info-box {
            background-color: #edf2f7;
            border-left: 4px solid #4299e1;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }

        .info-box p {
            font-size: 14px;
            color: #4a5568;
            margin: 0;
        }

        .footer {
            background-color: #2d3748;
            color: #a0aec0;
            padding: 30px;
            text-align: center;
            font-size: 14px;
        }

        .footer p {
            margin-bottom: 10px;
        }

        .footer a {
            color: #667eea;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .social-links {
            margin-top: 20px;
        }

        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #a0aec0;
            font-size: 18px;
        }

        @media (max-width: 600px) {
            .email-container {
                margin: 10px;
                border-radius: 8px;
            }

            .header,
            .content,
            .footer {
                padding: 20px;
            }

            .header h1 {
                font-size: 24px;
            }

            .verification-section {
                padding: 20px;
            }

            .verification-button {
                padding: 12px 25px;
                font-size: 15px;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>{{ config('app.name', 'Aura') }}</h1>
            <p>{{ __('auth.welcome_to_platform') }}</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="welcome-message">
                <h2>{{ __('auth.welcome') }}, {{ $user->name }}!</h2>
                <p>{{ __('auth.verify_email_message') }}</p>
            </div>

            <div class="verification-section">
                <h3 style="color: #2d3748; margin-bottom: 15px;">{{ __('auth.verify_email_title') }}</h3>
                <p style="margin-bottom: 25px; color: #718096;">{{ __('auth.verify_email_action') }}</p>

                <a href="{{ $verificationUrl }}" class="verification-button text-white" style="color: white !important;">
                    {{ __('auth.verify_email_button') }}
                </a>
            </div>

            <div class="info-box">
                <p><strong>{{ __('auth.security_note') }}</strong> {{ __('auth.verification_expire_notice') }}</p>
            </div>

            <div style="text-align: center; margin-top: 30px;">
                <p style="color: #718096; font-size: 14px;">
                    {{ __('auth.manual_link_instruction') }}<br>
                    <a href="{{ $verificationUrl }}" style="color: #667eea; word-break: break-all; font-size: 12px;">{{ $verificationUrl }}</a>
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Aura') }}. {{ __('auth.all_rights_reserved') }}</p>
            <p>{{ __('auth.automated_email_notice') }}</p>

            @if(config('app.url'))
            <p> <a href="{{ config('app.url') }}">{{ __('auth.visit_website') }}</a>
                <!-- <a href="{{ config('app.url') }}/contact">{{ __('auth.contact_support') }}</a> -->
            </p>
            @endif
        </div>
    </div>
</body>

</html>
