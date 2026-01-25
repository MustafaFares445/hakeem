<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>{{ __('Reset Password Notification') }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #1F2937;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
        }
        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .email-header {
            background-color: #005F5A;
            padding: 32px 24px;
            text-align: center;
        }
        .email-logo {
            max-width: 200px;
            height: auto;
            display: block;
            margin: 0 auto;
        }
        .email-body {
            padding: 40px 24px;
        }
        .email-content {
            color: #1F2937;
            font-size: 16px;
            line-height: 1.6;
        }
        .otp-code {
            background-color: #f9fafb;
            border: 2px solid #20ADA5;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 24px 0;
            font-size: 32px;
            font-weight: 600;
            letter-spacing: 4px;
            color: #005F5A;
            font-family: 'Courier New', monospace;
        }
        .email-footer {
            background-color: #f9fafb;
            padding: 24px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .email-footer-text {
            color: #4B5563;
            font-size: 14px;
            margin: 8px 0;
        }
        @media only screen and (max-width: 600px) {
            .email-body {
                padding: 24px 16px;
            }
            .email-header {
                padding: 24px 16px;
            }
            .otp-code {
                font-size: 24px;
                letter-spacing: 2px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-header">
            <img src="{{ asset('images/emails/logo.png') }}" alt="{{ config('app.name') }}" class="email-logo">
        </div>
        <div class="email-body">
            <div class="email-content">
                <h1 style="color: #005F5A; font-size: 24px; margin: 0 0 16px 0;">{{ __('Reset Password Notification') }}</h1>

                <p style="color: #1F2937; margin: 16px 0;">
                    {{ __('Please use the following code to reset your password:') }}
                </p>

                <div class="otp-code">
                    {{ $otp }}
                </div>

                <p style="color: #4B5563; margin: 16px 0; font-size: 14px;">
                    {{ __('This code will expire in 15 minutes.') }}
                </p>

                <p style="color: #4B5563; margin: 24px 0 0 0; font-size: 14px;">
                    {{ __('If you did not request a password reset, no further action is required.') }}
                </p>
            </div>
        </div>
        <div class="email-footer">
            <p class="email-footer-text">
                <strong>{{ config('app.name') }}</strong>
            </p>
            <p class="email-footer-text" style="color: #4B5563; font-size: 12px;">
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
