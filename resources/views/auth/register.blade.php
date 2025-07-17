<x-guest-layout>
    <style>
        body {
            background-color: #1f2937; /* Tailwind bg-gray-900 */
            font-family: Arial, sans-serif;
        }

        .form-container {
            max-width: 400px;
            margin: 50px auto;
            background-color: #2d3748; /* Tailwind bg-gray-800 */
            color: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        }

        .form-container h2 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 5px;
            text-transform: lowercase;
        }

        .form-container p {
            text-align: center;
            font-size: 14px;
            margin-bottom: 20px;
            color: #cbd5e0;
        }

        label {
            font-size: 14px;
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: none;
            margin-bottom: 15px;
            font-size: 14px;
            color: #000; /* Ubah warna teks input menjadi hitam */
        }

        button {
            width: 100%;
            background-color: #000;
            color: white;
            padding: 10px;
            font-size: 14px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        button:hover {
            background-color: #333;
        }

        .already {
            text-align: center;
            margin-top: 15px;
            font-size: 13px;
            color: #a0aec0;
        }

        .already a {
            color: #cbd5e0;
            text-decoration: underline;
        }
    </style>

    <div class="form-container">
        <h2>Register</h2>
        <p>Create Your Account</p>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Username -->
            <div>
                <label for="name">Username</label>
                <input id="name" type="text" name="name" :value="old('name')" required autofocus placeholder="Username">
                <x-input-error :messages="$errors->get('name')" class="text-red-400 text-sm" />
            </div>

            <!-- Email -->
            <div>
                <label for="email">Email</label>
                <input id="email" type="email" name="email" :value="old('email')" required placeholder="Email">
                <x-input-error :messages="$errors->get('email')" class="text-red-400 text-sm" />
            </div>

            <!-- Password -->
            <div>
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required placeholder="Password">
                <x-input-error :messages="$errors->get('password')" class="text-red-400 text-sm" />
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation">Confirm Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required placeholder="Confirm Password">
                <x-input-error :messages="$errors->get('password_confirmation')" class="text-red-400 text-sm" />
            </div>

            <button type="submit">Register</button>

            <div class="already">
                <a href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>
