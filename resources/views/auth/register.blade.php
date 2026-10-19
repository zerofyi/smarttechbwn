@extends('layouts.app')

@section('title', 'Register')

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

@section('content')
<div class="signup-container">
    {{-- Global Alert Banner --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->has('error'))
        <div class="alert alert-danger">{{ $errors->first('error') }}</div>
    @endif

    {{-- STEP 1 --}}
    <div id="step-one-container" class="step-container {{ $step == 1 ? '' : 'd-none' }}">
        <h2>Create a new Account</h2>
        <form id="step1Form" method="POST" action="{{ route('register.submit') }}">
            @csrf
            <div class="form-group">
                <label for="name">Name</label>
                <input required type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Enter your name" value="{{ old('name') }}">
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input required type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Enter your email" value="{{ old('email') }}">
                @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="mobile">Mobile</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">+91</span>
                    </div>
                    <input required type="text" class="form-control @error('mobile') is-invalid @enderror" id="mobile" name="mobile" placeholder="Enter your mobile number" value="{{ old('mobile') }}">
                    @error('mobile')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="form-check form-group">
                <input type="checkbox" class="form-check-input @error('terms') is-invalid @enderror" id="terms" name="terms" required>
                <label class="form-check-label" for="terms">
                    I agree to the <a href="{{ route('terms') }}" target="_blank" class="link">Terms and Conditions</a>
                </label>
                @error('terms')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn primary">Next</button>
        </form>
        <div class="divider">or</div>
        <a href="{{ route('login') }}" class="refer">Already Have An Account?</a>
    </div>

    {{-- STEP 2 --}}
    <div id="step-two-container" class="step-container {{ $step == 2 ? '' : 'd-none' }}">
        <h2>Verify Your Mobile Number</h2>
        <form id="step2Form" method="POST" action="{{ route('register.verify.submit') }}">
            @csrf
            <div class="form-group">
                <label for="otp">Enter 6-digit OTP</label>
                <div id="otp-container" class="otp-input-fields">
                    <input type="text" class="otp-input otp__digit" maxlength="1" autofocus>
                    <input type="text" class="otp-input otp__digit" maxlength="1">
                    <input type="text" class="otp-input otp__digit" maxlength="1">
                    <input type="text" class="otp-input otp__digit" maxlength="1">
                    <input type="text" class="otp-input otp__digit" maxlength="1">
                    <input type="text" class="otp-input otp__digit" maxlength="1">
                </div>
                <input type="hidden" name="otp" id="otp-hidden-input">
            </div>
            <button type="submit" class="btn primary mb-2">Verify OTP</button>
        </form>

        <form id="resendOtpForm" method="POST" action="{{ route('register.resend') }}" class="text-center mt-2">
            @csrf
            <button type="submit" id="resendBtn" class="btn btn-link p-0" disabled>
                Resend OTP <span id="cooldownTimer">(60s)</span>
            </button>
        </form>
    </div>

    {{-- STEP 3 --}}
    <div id="step-three-container" class="step-container {{ $step == 3 ? '' : 'd-none' }}">
        <h2>Set Your Login Password</h2>
        @if (isset($user))
        <form id="step3Form" method="POST" action="{{ route('register.password.submit', $user->user_id) }}">
            @csrf
            <div class="form-group">
                <label for="password">Password</label>
                <div class="position-relative">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Create a password" required>
                    <span class="password-toggle" onclick="togglePassword('password', 'togglePasswordIcon1')">
                        <i class="fa fa-eye" id="togglePasswordIcon1"></i>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <label for="confirmPassword">Confirm Password</label>
                <div class="position-relative">
                    <input type="password" class="form-control" id="confirmPassword" name="password_confirmation" placeholder="Confirm your password" required>
                    <span class="password-toggle" onclick="togglePassword('confirmPassword', 'togglePasswordIcon2')">
                        <i class="fa fa-eye" id="togglePasswordIcon2"></i>
                    </span>
                </div>
                <small id="passwordMatchMessage" class="form-text"></small>
            </div>
            <button type="submit" class="btn primary" disabled id="submitButton">Submit</button>
        </form>
        @endif
    </div>
</div>
@endsection

@section('page-script')
<script>
    function togglePassword(inputId, iconId) {
        const passwordInput = document.getElementById(inputId);
        const togglePasswordIcon = document.getElementById(iconId);
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

    // OTP Input Navigation Logic
    var otp_inputs = document.querySelectorAll(".otp__digit");
    otp_inputs.forEach((input, index) => {
        input.addEventListener("input", (event) => {
            if (event.target.value.length === 1 && index < otp_inputs.length - 1) {
                otp_inputs[index + 1].focus();
            }
        });

        input.addEventListener("keydown", (event) => {
            if (event.key === "Backspace" && input.value.length === 0 && index > 0) {
                otp_inputs[index - 1].focus();
            }
        });
    });

    // OTP Form Submission Gathering
    const step2Form = document.getElementById('step2Form');
    if (step2Form) {
        step2Form.addEventListener('submit', function (event) {
            let otpValue = '';
            otp_inputs.forEach(input => {
                otpValue += input.value;
            });
            document.getElementById('otp-hidden-input').value = otpValue;

            if (otpValue.length !== 6) {
                event.preventDefault();
                alert('Please enter a valid 6-digit OTP code.');
            }
        });
    }

    // Step 3 Password Validation
    document.addEventListener('DOMContentLoaded', function () {
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirmPassword');
        const passwordMatchMessage = document.getElementById('passwordMatchMessage');
        const submitButton = document.getElementById('submitButton');

        if (passwordInput && confirmPasswordInput) {
            function validatePasswords() {
                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;

                if (password.length < 6) {
                    passwordInput.style.borderColor = 'red';
                    passwordMatchMessage.textContent = 'Password must be at least 6 characters long.';
                    passwordMatchMessage.style.color = 'red';
                    submitButton.disabled = true;
                } else {
                    passwordInput.style.borderColor = 'green';
                }

                if (password !== confirmPassword) {
                    confirmPasswordInput.style.borderColor = 'red';
                    passwordMatchMessage.textContent = 'Passwords do not match.';
                    passwordMatchMessage.style.color = 'red';
                    submitButton.disabled = true;
                } else if (password.length >= 6 && password === confirmPassword) {
                    confirmPasswordInput.style.borderColor = 'green';
                    passwordMatchMessage.textContent = 'Passwords match.';
                    passwordMatchMessage.style.color = 'green';
                    submitButton.disabled = false;
                }
            }

            passwordInput.addEventListener('input', validatePasswords);
            confirmPasswordInput.addEventListener('input', validatePasswords);
        }

        // Resend OTP Cooldown Timer (Step 2)
        const resendBtn = document.getElementById('resendBtn');
        const timerSpan = document.getElementById('cooldownTimer');
        if (resendBtn && timerSpan) {
            let secondsLeft = 60;
            const timer = setInterval(() => {
                secondsLeft--;
                if (secondsLeft <= 0) {
                    clearInterval(timer);
                    resendBtn.disabled = false;
                    timerSpan.textContent = '';
                } else {
                    timerSpan.textContent = `(${secondsLeft}s)`;
                }
            }, 1000);
        }
    });
</script>
@endsection
