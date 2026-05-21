<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup 2FA - AccountHub</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body {
            background-color: var(--background);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            font-family: 'Inter', sans-serif;
        }
        .auth-container {
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 400px;
            border: 1px solid var(--glass-border);
            text-align: center;
        }
        .auth-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
            justify-content: center;
        }
        .auth-logo-icon {
            width: 32px;
            height: 32px;
            background: var(--brand-primary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
        }
        .auth-logo-text {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-primary);
        }
        .qr-code-container {
            margin: 24px auto;
            padding: 16px;
            background: #f8fafc;
            border-radius: 12px;
            display: inline-block;
        }
        .qr-code-container svg {
            width: 200px;
            height: 200px;
        }
        .secret-key {
            background: #f1f5f9;
            padding: 8px 12px;
            border-radius: 6px;
            font-family: monospace;
            font-size: 1rem;
            color: #334155;
            letter-spacing: 2px;
            margin-bottom: 24px;
            display: inline-block;
        }
        .btn-primary {
            width: 100%;
            padding: 12px;
            background: var(--brand-primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary:hover {
            background: #1d4ed8;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-logo">
            <div class="auth-logo-icon">AH</div>
            <div class="auth-logo-text">AccountHub</div>
        </div>
        
        <h2 style="margin-bottom: 12px; color: var(--text-primary); font-size: 1.25rem;">Set up 2FA (Optional)</h2>
        <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 24px;">Scan the QR code below with <strong>Google Authenticator</strong>, <strong>Microsoft Authenticator</strong>, or any TOTP app to secure your account.</p>

        <div class="qr-code-container">
            {!! $qrCodeSvg !!}
        </div>

        <p style="color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 8px;">Or enter this code manually:</p>
        <div class="secret-key">{{ $secret }}</div>

        <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 12px;">
            <a href="{{ route('2fa.verify') }}" class="btn-primary">I've scanned it, continue</a>
            
            <form method="POST" action="{{ route('2fa.skip') }}">
                @csrf
                <button type="submit" style="background: transparent; color: var(--text-secondary); border: none; font-size: 0.9rem; cursor: pointer; text-decoration: underline;">Skip for now</button>
            </form>
        </div>
    </div>
</body>
</html>
