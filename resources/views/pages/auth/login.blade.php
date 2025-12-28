@extends('layouts.auth')

@section('content')
<div class="card-glass max-w-md w-full">
    <div class="text-center mb-8">
        <div class="flex items-center justify-center gap-3 mb-4">
            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
            <span class="text-2xl font-bold gradient-text">WatchMyApi</span>
        </div>
        <h2 class="text-3xl font-bold mb-2 text-white">Welcome Back</h2>
        <p class="text-gray-400">Sign in to manage your API infrastructure</p>
    </div>

    <div id="login-error-container"></div>

    <form method="POST" action="/api/auth/login" class="space-y-6" id="login-form">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">Email Address</label>
            <input type="email" name="email" required id="email" class="input" placeholder="name@company.com">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">Password</label>
            <input type="password" name="password" required id="password" class="input" placeholder="••••••••">
        </div>

        <button type="submit" id="login-submit" class="w-full py-4 bg-purple-600 hover:bg-purple-500 text-zinc-200 rounded-full font-semibold transition-all duration-200 shadow-lg">
            Sign In
        </button>

        <div class="text-center mt-6">
            <a href="/" class="text-sm text-gray-400 hover:text-white transition-colors">
                ← Back to Home
            </a>
        </div>
    </form>
</div>
@endsection
