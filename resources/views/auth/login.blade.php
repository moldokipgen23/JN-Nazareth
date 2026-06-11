<x-guest-layout>
    <div style="text-align:center;margin-bottom:20px;">
        <h1 style="font-size:17px;font-weight:700;color:#f1f5f9;margin:0 0 4px;">Admin Portal</h1>
        <p style="font-size:13px;color:#94a3b8;margin:0;">Sign in to manage the website.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" autocomplete="on">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember" value="1">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }} <span style="color:#94a3b8;font-size:11px;">(keeps you signed in for 30 days)</span></span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-4">
            <div style="font-size:11px;color:#64748b;">Forgot password? Contact your school administration.</div>
            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
