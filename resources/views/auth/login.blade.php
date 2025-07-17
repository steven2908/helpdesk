<x-guest-layout>
    
    <style>
    body {
        background-color: #000;
        margin: 0;
        padding: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .login-container {
        max-width: 400px;
        margin: 5% auto;
        background-color: #1e1e1e;
        border-radius: 10px;
        padding: 2rem;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
        color: white;
        text-align: center;
    }

    .login-container h2 {
        font-size: 1.8rem;
        margin-bottom: 0.5rem;
    }

    .login-container p {
        color: #ccc;
        margin-bottom: 2rem;
    }

    .login-container label {
        display: block;
        text-align: left;
        margin-bottom: 0.25rem;
        font-weight: 500;
    }

    .login-container input[type="email"],
    .login-container input[type="password"] {
        width: 100%;
        padding: 0.6rem;
        border-radius: 6px;
        border: none;
        margin-bottom: 1.2rem;
        background-color: #2a2a2a;
        color: white;
        caret-color: white;
        font-size: 1rem;
    }

    .login-container a.forgot-password {
        display: block;
        text-align: right;
        font-size: 0.875rem;
        color: #60a5fa;
        margin-top: -1rem;
        margin-bottom: 1rem;
        text-decoration: none;
    }

    .login-container a.forgot-password:hover {
        text-decoration: underline;
    }

    .login-container button {
        width: 100%;
        padding: 0.7rem;
        border: none;
        border-radius: 6px;
        font-weight: bold;
        cursor: pointer;
        margin-bottom: 1rem;
        font-size: 1rem;
    }

    .btn-login {
        background-color: #3b82f6;
        color: white;
    }

    .btn-login:hover {
        background-color: #2563eb;
    }

    .btn-register {
        background-color: #000;
        color: white;
        border: 1px solid white;
    }

    .btn-register:hover {
        background-color: #111;
    }

    .or-divider {
        text-align: center;
        color: #aaa;
        margin: 1.5rem 0;
        position: relative;
        font-size: 0.875rem;
    }

    .or-divider::before,
    .or-divider::after {
        content: "";
        position: absolute;
        top: 50%;
        width: 40%;
        height: 1px;
        background: #555;
    }

    .or-divider::before {
        left: 0;
    }

    .or-divider::after {
        right: 0;
    }

    .login-logo {
    max-width: 180px;
    height: auto;
    margin: 0 auto 1.5rem auto;
    display: block;
}


    /* Responsive Styles */
    @media (max-width: 480px) {
        .login-container {
            margin: 10% 1rem;
            padding: 1.5rem;
        }

        .login-container h2 {
            font-size: 1.5rem;
        }

        .login-container input,
        .login-container button {
            font-size: 0.95rem;
        }

        .or-divider::before,
        .or-divider::after {
            width: 30%;
        }
    }
</style>


    <div class="login-container">
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <h2>Login</h2>
        <p>sign in to your account</p>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <label for="email">Email</label>
                <input id="email" type="email" name="email" placeholder="masukkan Email" :value="old('email')" required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <label for="password">Password</label>
                <input id="password" type="password" name="password" placeholder="masukkan Password" required />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            @if (Route::has('password.request'))
                <a class="forgot-password" href="{{ route('password.request') }}">
                    lupa password ?
                </a>
            @endif

            <button type="submit" class="btn-login">login</button>

            <div class="or-divider">OR</div>

            <a href="{{ route('register') }}">
                <button type="button" class="btn-register">register</button>
            </a>
        </form>
    </div>
</x-guest-layout>
