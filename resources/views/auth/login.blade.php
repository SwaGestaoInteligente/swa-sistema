<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0c2e69">
    <title>Login | SWA</title>
    <style>
        :root {
            --bg: #edf3fb;
            --panel: #ffffff;
            --text: #0f2144;
            --muted: #62779a;
            --line: #d5e0ef;
            --primary: #0c2e69;
            --shadow: 0 18px 34px rgba(15, 33, 68, 0.10);
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100dvh;
            display: grid;
            place-items: center;
            padding: 18px;
            background:
                radial-gradient(circle at 100% 0%, rgba(167, 196, 236, 0.18) 0%, rgba(167, 196, 236, 0) 34%),
                linear-gradient(180deg, #f3f7fd 0%, var(--bg) 100%);
            color: var(--text);
            font-family: "Segoe UI", sans-serif;
        }
        .login-card {
            width: min(440px, 100%);
            border: 1px solid var(--line);
            border-radius: 20px;
            padding: 22px;
            background: linear-gradient(180deg, #ffffff 0%, #f9fbfe 100%);
            box-shadow: var(--shadow);
        }
        .brand {
            display: grid;
            justify-items: center;
            gap: 12px;
            margin-bottom: 18px;
            text-align: center;
        }
        .brand img {
            width: min(220px, 72%);
            height: auto;
        }
        .brand h1 {
            margin: 0;
            font-size: 34px;
            line-height: 1;
            color: var(--primary);
        }
        .brand p {
            margin: 0;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.6;
        }
        .error {
            border: 1px solid #f1c4c1;
            background: #fff1f0;
            color: #9f2d2d;
            border-radius: 12px;
            padding: 12px 14px;
            font-size: 14px;
            margin-bottom: 14px;
        }
        .field { margin-bottom: 14px; }
        label {
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
            font-weight: 700;
            color: #375173;
        }
        input {
            width: 100%;
            min-height: 48px;
            padding: 12px 14px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: #fff;
            color: var(--text);
            font-size: 15px;
        }
        input:focus {
            outline: 2px solid rgba(12, 46, 105, 0.12);
            border-color: #95abcf;
        }
        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear {
            display: none;
        }
        .password-wrap {
            position: relative;
        }
        .password-wrap input {
            padding-right: 48px;
        }
        .toggle-password {
            position: absolute;
            right: 9px;
            top: 50%;
            transform: translateY(-50%);
            width: 34px;
            height: 34px;
            border: 0;
            border-radius: 10px;
            background: transparent;
            color: #7b8faf;
            display: grid;
            place-items: center;
            cursor: pointer;
        }
        .toggle-password:hover {
            background: #edf2fb;
            color: #2f4f7f;
        }
        .toggle-password svg {
            width: 18px;
            height: 18px;
            stroke: currentColor;
            fill: none;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
        }
        .toggle-password .eye-off { display: none; }
        .toggle-password.is-visible .eye-on { display: none; }
        .toggle-password.is-visible .eye-off { display: block; }
        .row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin: 8px 0 16px;
            color: var(--muted);
            font-size: 13px;
        }
        .checkbox {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .checkbox input {
            width: auto;
            min-height: auto;
            margin: 0;
        }
        .submit-btn {
            width: 100%;
            border: 0;
            border-radius: 14px;
            min-height: 50px;
            padding: 12px 16px;
            background: linear-gradient(90deg, #0f3e85 0%, #205b99 100%);
            color: #fff;
            font-size: 15px;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 12px 22px rgba(21, 69, 133, 0.14);
        }
        .submit-btn:hover {
            filter: brightness(1.02);
        }
        .note {
            margin-top: 14px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.6;
            text-align: center;
        }
        @media (max-width: 560px) {
            body {
                padding: 12px;
            }
            .login-card {
                border-radius: 16px;
                padding: 16px;
            }
            .row {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <main class="login-card">
        <div class="brand">
            <img src="{{ asset('images/swa-logo.jpeg') }}" alt="SWA logo">
            <div>
                <h1>SWA</h1>
                <p>Painel de vistoria e compliance.</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login.store') }}">
            @csrf
            <div class="field">
                <label for="email">E-mail</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="field">
                <label for="password">Senha</label>
                <div class="password-wrap">
                    <input id="password" name="password" type="password" required>
                    <button type="button" class="toggle-password" id="togglePassword" aria-label="Mostrar senha">
                        <svg class="eye-on" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6-10-6-10-6Z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        <svg class="eye-off" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M3 3l18 18"/>
                            <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"/>
                            <path d="M9.4 5.4A11.5 11.5 0 0 1 12 5c6.5 0 10 7 10 7a19.5 19.5 0 0 1-4 4.8"/>
                            <path d="M6.5 6.5C4.1 8 2 12 2 12s3.5 6 10 6c1.5 0 2.9-.3 4.1-.8"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="row">
                <label class="checkbox" for="remember">
                    <input id="remember" name="remember" type="checkbox" value="1">
                    Lembrar de mim
                </label>
                <span>Uso interno.</span>
            </div>

            <button class="submit-btn" type="submit">Entrar</button>
        </form>

        <div class="note">Entre e escolha o condomínio para continuar.</div>
    </main>

    <script>
        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('togglePassword');

        if (passwordInput && togglePassword) {
            togglePassword.addEventListener('click', () => {
                const isVisible = passwordInput.type === 'text';
                passwordInput.type = isVisible ? 'password' : 'text';
                togglePassword.classList.toggle('is-visible', !isVisible);
                togglePassword.setAttribute('aria-label', isVisible ? 'Mostrar senha' : 'Ocultar senha');
            });
        }
    </script>
</body>
</html>
