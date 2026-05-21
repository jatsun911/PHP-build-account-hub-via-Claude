<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify 2FA - AccountHub</title>
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
        }
        .auth-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 32px;
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
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-primary);
            font-size: 0.875rem;
        }
        .form-input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 1.25rem;
            letter-spacing: 4px;
            text-align: center;
            transition: border-color 0.2s, box-shadow 0.2s;
            box-sizing: border-box;
        }
        .form-input:focus {
            outline: none;
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
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
            transition: background 0.2s;
        }
        .btn-primary:hover {
            background: #1d4ed8;
        }
        .error-message {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 8px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-logo">
            <div class="auth-logo-icon">AH</div>
            <div class="auth-logo-text">AccountHub</div>
        </div>
        
        <h2 style="text-align: center; margin-bottom: 12px; color: var(--text-primary); font-size: 1.5rem;">Two-Factor Authentication</h2>
        <p style="text-align: center; color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 24px;">Enter the 6-digit code from Google Authenticator or Microsoft Authenticator.</p>

        <form method="POST" action="{{ url('/2fa/verify') }}">
            @csrf
            
            <div class="form-group">
                <input class="form-input" id="one_time_password" type="text" name="one_time_password" required autofocus autocomplete="off" maxlength="6">
                @error('one_time_password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-primary">Verify Code</button>
        </form>
    </div>
</body>
</html>
