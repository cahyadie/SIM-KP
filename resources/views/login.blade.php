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

        /* --- RIGHT PANEL (MENGGUNAKAN BACKGROUND GAMBAR) --- */
        .brand-panel {
            flex: 1;
            /* Menggunakan overlay gradien gelap + fungsi asset() bawaan Laravel */
            background:url("{{ asset('bg-kanan.png') }}");            
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 60px 40px;
            position: relative;
            overflow: hidden;
        }

        /* Hapus atau sembunyikan dekorasi lingkaran bawaan jika dirasa bertabrakan dengan foto */
        .brand-panel::before,
        .brand-panel::after {
            display: none; 
        }

        /* --- LEFT FORM PANEL --- */
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

        /* --- LOGO FORM --- */
        .form-logo {
            height: 30px;
            width: auto;
            margin-bottom: 20px;
            display: block;
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
            font-weight: 600;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        /* --- RESPONSIVE --- */
        @media (max-width: 800px) {
            body { flex-direction: column; }
            .brand-panel {
                padding: 40px 30px;
                min-height: 280px;
            }
            .brand-icon { width: 60px; height: 60px; margin-bottom: 20px; }
            .brand-icon svg { width: 30px; height: 30px; }
            .brand-title { font-size: 22px; }
            .brand-desc { font-size: 14px; margin-bottom: 0; }
            .form-panel { padding: 24px 16px; }
            .form-card { padding: 32px 24px; }
        }
    </style>
</head>
<body>
    <div class="form-panel">
        <div class="form-card">
            <div class="form-header">
                <img src="{{ asset('logo-form.png') }}" alt="Logo SIM-KP" class="form-logo">
                <h2>Login</h2>
                <p>Masuk untuk melanjutkan ke dashboard SIM-KP</p>
            </div>

            @if($errors->any())
                <div class="error">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" id="loginForm">
                @csrf

                <div class="input-group">
                    <label for="email">Email</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="4" width="20" height="16" rx="2"/>
                            <path d="M22 4l-10 8L2 4"/>
                        </svg>
                        <input type="email" id="email" name="email" placeholder="contoh@umy.ac.id" required autocomplete="email">
                    </div>
                </div>

                <div class="input-group">
                    <label for="password">
                        Password
                        <a href="#" class="forgot-link">Lupa password?</a>
                    </label>
                    <div class="input-wrapper">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0110 0v4"/>
                        </svg>
                        <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                        <button type="button" class="password-toggle" id="togglePassword" aria-label="Toggle password visibility">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-primary" id="submitBtn">
                    <span class="btn-text">Masuk</span>
                    <div class="spinner"></div>
                </button>
            </form>

            <div class="divider">
                <span class="divider-line"></span>
                <span class="divider-text">Khusus mahasiswa</span>
                <span class="divider-line"></span>
            </div>

            <a href="{{ route('auth.microsoft') }}" class="btn-microsoft">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 23 23">
                    <path fill="#f35325" d="M1 1h10v10H1z"/>
                    <path fill="#81bc06" d="M12 1h10v10H12z"/>
                    <path fill="#05a6f0" d="M1 12h10v10H1z"/>
                    <path fill="#ffba08" d="M12 12h10v10H12z"/>
                </svg>
                Masuk dengan Akun Kampus
            </a>
        </div>
    </div>

    <div class="brand-panel">
    </div>

    <script>
        // Password toggle
        document.getElementById('togglePassword').addEventListener('click', function() {
            const input = document.getElementById('password');
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
        });

        // Loading state on submit
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('submitBtn');
            btn.classList.add('loading');
        });
    </script>
</body>
</html>