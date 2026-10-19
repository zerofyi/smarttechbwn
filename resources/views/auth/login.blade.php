@extends('layouts.app')

{{-- PAGE TITLE HERE --}}
@section('title', 'Login')

{{-- VENDOR CSS START --}}
@section('vendor-style')
@endsection
{{-- VENDOR CSS END --}}

{{-- PAGE CSS START --}}
@section('page-style')
<link href="../../../custom/assets/css/main.css" type="text/css" rel="stylesheet">
<style>
    body {
        background: #8B2500;
        background: linear-gradient(225deg, rgba(255, 68, 0, 0.2) 0%, rgba(255, 77, 0, 0.1) 35%, rgba(255, 51, 0, 0.02) 100%);
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }
</style>
@endsection
{{-- PAGE CSS END --}}

{{-- CONTENT START --}}
@section('content')
<div class="login-container">
    <h2>Sign in to your Account</h2>
    <form action="{{ route('login') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="login">Mobile/Email</label>
            <input type="text" class="form-control @error('login') is-invalid @enderror" id="login" name="login" placeholder="Enter your mobile or email" value="{{ old('login') }}" required>
            @error('login')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <div class="position-relative">
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Enter your password" required>
                <span class="password-toggle" onclick="togglePassword()">
                    <i class="fa fa-eye" id="togglePasswordIcon"></i>
                </span>
            </div>
            @error('password')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-check form-group">
            <input type="checkbox" class="form-check-input" id="rememberMe" name="remember" {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label" for="rememberMe">Remember Me</label>
        </div>

        <button type="submit" class="btn primary w-100">Sign In</button>

        <div class="text-center mt-3">
            <a href="{{ route('forgot-password') }}" class="link">Forgot Password?</a>
        </div>
    </form>

    <div class="divider my-4 text-center">or</div>

    <a href="{{ route('register.form') }}" class="refer">Create New Account</a>
</div>
@endsection
{{-- CONTENT END --}}

{{-- VENDOR JS START --}}
@section('vendor-script')
@endsection
{{-- VENDOR JS END --}}

{{-- PAGE JS START --}}
@section('page-script')
<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const togglePasswordIcon = document.getElementById('togglePasswordIcon');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            togglePasswordIcon.classList.remove('fa-eye');
            togglePasswordIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            togglePasswordIcon.classList.remove('fa-eye-slash');
            togglePasswordIcon.classList.add('fa-eye');
        }
    }
</script>
@endsection
{{-- PAGE JS END --}}
