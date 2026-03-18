
<x-layouts::app :title="__('Log in')">
    @push('style')
        <style>
            * {
                font-family: 'Tajawal', sans-serif;
            }
            
            .bg-gradient-auth {
                background: linear-gradient(135deg, #10b981 0%, #3b82f6 50%, #8b5cf6 100%);
            }
            
            .bg-gradient-primary {
                background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            }
            
            .bg-grid-pattern {
                background-image: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
                background-size: 20px 20px;
            }
            
            input:focus, select:focus, textarea:focus {
                outline: none;
            }
            
            .password-strength-weak { background-color: #ef4444; }
            .password-strength-fair { background-color: #f59e0b; }
            .password-strength-good { background-color: #3b82f6; }
            .password-strength-strong { background-color: #08ba7f; }
            
            .spinner {
                border: 2px solid rgba(255,255,255,0.3);
                border-top-color: white;
                border-radius: 50%;
                animation: spin 0.8s linear infinite;
            }
            
            @keyframes spin {
                to { transform: rotate(360deg); }
            }
            
            /* Custom scrollbar */
            ::-webkit-scrollbar {
                width: 8px;
            }
            
            ::-webkit-scrollbar-track {
                background: #f1f5f9;
            }
            
            ::-webkit-scrollbar-thumb {
                background: #10b981;
                border-radius: 4px;
            }
            
            ::-webkit-scrollbar-thumb:hover {
                background: #059669;
            }
        </style>
    @endpush
    @livewire('authlogin')
   
</x-layouts::app>
{{-- <x-layouts::auth :title="__('Log in')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Log in to your account')" :description="__('Enter your email and password below to log in')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Email address')"
                :value="old('email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="email@example.com"
            />

            <!-- Password -->
            <div class="relative">
                <flux:input
                    name="password"
                    :label="__('Password')"
                    type="password"
                    required
                    autocomplete="current-password"
                    :placeholder="__('Password')"
                    viewable
                />

                @if (Route::has('password.request'))
                    <flux:link class="absolute top-0 text-sm end-0" :href="route('password.request')" wire:navigate>
                        {{ __('Forgot your password?') }}
                    </flux:link>
                @endif
            </div>

            <!-- Remember Me -->
            <flux:checkbox name="remember" :label="__('Remember me')" :checked="old('remember')" />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
                    {{ __('Log in') }}
                </flux:button>
            </div>
        </form>

        @if (Route::has('register'))
            <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-600 dark:text-zinc-400">
                <span>{{ __('Don\'t have an account?') }}</span>
                <flux:link :href="route('register')" wire:navigate>{{ __('Sign up') }}</flux:link>
            </div>
        @endif
    </div>
</x-layouts::auth> --}}
