<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login SIM-KP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            display: flex;
            min-height: 100vh;
            background: #f5f7fb;
            color: #1e293b;
        }

        /* --- LEFT ILLUSTRATION PANEL --- */
        .brand-panel {
            flex: 1;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 60px 40px;
            position: relative;
            overflow: hidden;
        }

        .brand-panel::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0, 75, 35, 0.15) 0%, transparent 70%);
            top: -100px;
            right: -100px;
        }

        .brand-panel::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0, 107, 46, 0.1) 0%, transparent 70%);
            bottom: -80px;
            left: -80px;
        }

        .brand-content {
            position: relative;
            z-index: 1;
            text-align: center;
            max-width: 420px;
        }

        .brand-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #004b23, #007135);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 28px;
            box-shadow: 0 12px 40px rgba(0, 75, 35, 0.35);
        }

        .brand-icon svg {
            width: 40px;
            height: 40px;
            fill: white;
        }

        .brand-title {
            font-size: 28px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.5px;
            margin-bottom: 12px;
        }

        .brand-desc {
            font-size: 15px;
            line-height: 1.7;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 40px;
        }

        .brand-features {
            display: flex;
            flex-direction: column;
            gap: 16px;
            text-align: left;
            max-width: 300px;
            margin: 0 auto;
        }

        .brand-feature {
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255, 255, 255, 0.75);
            font-size: 14px;
            font-weight: 500;
        }

        .brand-feature .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #004b23;
            flex-shrink: 0;
        }

        .brand-feature:nth-child(2) .dot { background: #007135; }
        .brand-feature:nth-child(3) .dot { background: #004b23; }

        /* --- RIGHT FORM PANEL --- */
        .form-panel {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
            background: #f5f7fb;
        }

        .form-card {
            width: 100%;
            max-width: 400px;
            background: #ffffff;
            border-radius: 20px;
            padding: 44px 36px 36px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 8px 32px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(0, 0, 0, 0.04);
        }

        .form-header {
            margin-bottom: 32px;
        }

        .form-header h2 {
            font-size: 22px;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.4px;
            margin-bottom: 6px;
        }

        .form-header p {
            font-size: 14px;
            color: #64748b;
            line-height: 1.5;
        }

        /* --- ERROR --- */
        .error {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #fef2f2;
            color: #dc2626;
            font-size: 13px;
            font-weight: 500;
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 24px;
            border: 1px solid #fecaca;
        }

        .error svg {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
        }

        /* --- INPUT --- */
        .input-group {
            margin-bottom: 20px;
        }

        .input-group label {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }

        .input-group label .forgot-link {
            font-weight: 500;
            font-size: 12px;
            color: #004b23;
            text-decoration: none;
        }

        .input-group label .forgot-link:hover {
            text-decoration: underline;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            color: #94a3b8;
            pointer-events: none;
        }

        .input-wrapper input {
            width: 100%;
            padding: 12px 14px 12px 44px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            color: #0f172a;
            background: #f8fafc;
            transition: all 0.2s ease;
            outline: none;
        }

        .input-wrapper input:focus {
            border-color: #004b23;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(0, 75, 35, 0.1);
        }

        .input-wrapper input::placeholder {
            color: #94a3b8;
        }

        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #94a3b8;
            padding: 0;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s;
        }

        .password-toggle:hover {
            color: #64748b;
        }

        /* --- BUTTON --- */
        .btn-primary {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #003318, #004b23);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 4px;
            position: relative;
            overflow: hidden;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(0, 51, 24, 0.3);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-primary .spinner {
            display: none;
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
            position: absolute;
            left: 50%;
            top: 50%;
            margin: -9px 0 0 -9px;
        }

        .btn-primary.loading .btn-text { visibility: hidden; }
        .btn-primary.loading .spinner { display: block; }

        @keyframes spin { to { transform: rotate(360deg); } }

        /* --- DIVIDER --- */
        .divider {
            display: flex;
            align-items: center;
            margin: 24px 0;
            gap: 16px;
        }

        .divider-line {
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }

        .divider-text {
            font-size: 12px;
            font-weight: 500;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* --- MICROSOFT BUTTON --- */
        .btn-microsoft {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 12px;
            background: #ffffff;
            color: #1e293b;
            text-decoration: none;
            border-radius: 10px;
            border: 1.5px solid #e2e8f0;
            font-size: 14px;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s ease;
        }

        .btn-microsoft:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        }

        .btn-microsoft:active {
            transform: scale(0.99);
        }

        .btn-microsoft svg {
            width: 18px;
            height: 18px;
        }

        /* --- REGISTER LINK --- */
        .register-link {
            text-align: center;
            margin-top: 24px;
            font-size: 13px;
            color: #64748b;
        }

        .register-link a {
            color: #004b23;
