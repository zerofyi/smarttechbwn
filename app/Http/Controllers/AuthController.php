<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Otp;
use App\Models\User;
use App\Models\UserCart;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class AuthController extends Controller
{
    // Constant limits
    private const OTP_COOLDOWN_SECONDS = 60;
    private const OTP_MAX_ATTEMPTS = 5;
    private const OTP_EXPIRY_MINUTES = 10;

    public function showStepOne()
    {
        return view('auth.register', ['step' => 1]);
    }

    public function processStepOne(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'mobile' => 'required|string|unique:users,mobile|regex:/^[0-9]{10}$/',
            'terms' => 'required|accepted',
        ]);

        $mobile = $validated['mobile'];
        $email = $validated['email'];

        $otpRecord = Otp::where('mobile', $mobile)
            ->orWhere('email', $email)
            ->first();

        // 1. Cooldown Check (60 seconds between requests)
        if ($otpRecord && $otpRecord->last_request_at) {
            $secondsSinceLastRequest = Carbon::now()->diffInSeconds($otpRecord->last_request_at);
            if ($secondsSinceLastRequest < self::OTP_COOLDOWN_SECONDS) {
                $retryAfter = self::OTP_COOLDOWN_SECONDS - $secondsSinceLastRequest;
                return back()->withErrors(['error' => "Please wait {$retryAfter} seconds before requesting another OTP."])->withInput();
            }
        }

        // 2. Maximum Attempts Check (Reset if last request was > 6 hours ago)
        if ($otpRecord && $otpRecord->last_request_at) {
            if ($otpRecord->last_request_at->gt(Carbon::now()->subHours(6)) && $otpRecord->attempts >= self::OTP_MAX_ATTEMPTS) {
                return back()->withErrors(['error' => 'You have reached the maximum OTP request limit. Please try again after 6 hours.'])->withInput();
            }

            // Reset attempts counter if outside 6-hour window
            if ($otpRecord->last_request_at->lte(Carbon::now()->subHours(6))) {
                $otpRecord->attempts = 0;
            }
        }

        $generatedOtp = (string) rand(100000, 999999);

        // Dispatch SMS
        $smsSent = $this->sendOtpViaApi($mobile, $generatedOtp);

        if (!$smsSent) {
            return back()->withErrors(['error' => 'Failed to dispatch OTP SMS. Please verify your mobile number and try again.'])->withInput();
        }

        $otpData = [
            'name' => $validated['name'],
            'email' => $email,
            'mobile' => $mobile,
            'otp' => $generatedOtp,
            'expires_at' => Carbon::now()->addMinutes(self::OTP_EXPIRY_MINUTES),
            'attempts' => ($otpRecord ? $otpRecord->attempts : 0) + 1,
            'last_request_at' => Carbon::now(),
            'api_message' => 'OTP dispatched successfully',
        ];

        Otp::updateOrCreate(
            ['mobile' => $mobile],
            $otpData
        );

        $cookie = cookie('registration_data', json_encode([
            'name' => $validated['name'],
            'email' => $email,
            'mobile' => $mobile,
        ]), 30);

        return redirect()->route('register.verify.form')
            ->withCookie($cookie)
            ->with('success', 'OTP sent successfully! Please enter the 6-digit code below.');
    }

    public function showStepTwo(Request $request)
    {
        $registrationData = json_decode($request->cookie('registration_data'), true);

        if (!$registrationData) {
            return redirect()->route('register.form')
                ->withErrors(['error' => 'Your registration session has expired. Please restart registration.']);
        }

        return view('auth.register', [
            'step' => 2,
            'registrationData' => $registrationData
        ]);
    }

    public function processStepTwo(Request $request)
    {
        $registrationData = json_decode($request->cookie('registration_data'), true);

        if (!$registrationData) {
            return redirect()->route('register.form')
                ->withErrors(['error' => 'Your registration session has expired. Please restart registration.']);
        }

        $validated = $request->validate([
            'otp' => 'required|numeric|digits:6',
        ]);

        $otpRecord = Otp::where('mobile', $registrationData['mobile'])
            ->where('email', $registrationData['email'])
            ->first();

        if (!$otpRecord) {
            return redirect()->route('register.form')
                ->withErrors(['error' => 'Invalid verification context. Please restart registration.'])
                ->withCookie(Cookie::forget('registration_data'));
        }

        if (Carbon::now()->gt($otpRecord->expires_at)) {
            return redirect()->route('register.verify.form')
                ->withErrors(['error' => 'OTP has expired. Click "Resend OTP" to request a new code.']);
        }

        if ($otpRecord->otp !== $validated['otp']) {
            return back()->withErrors(['error' => 'Invalid OTP code. Please check and try again.']);
        }

        // Database Transaction: Create user, map customer, create cart
        $user = DB::transaction(function () use ($registrationData, $validated, $otpRecord) {
            $user = User::create([
                'name' => $registrationData['name'],
                'email' => $registrationData['email'],
                'mobile' => $registrationData['mobile'],
                'password' => Hash::make($validated['otp']),
                'role' => 'customer',
                'status' => 'active',
                'email_verified_at' => now(),
            ]);

            // Offline Customer Mapping Logic
            $existingCustomer = Customer::where('mobile', $registrationData['mobile'])->first();

            if ($existingCustomer) {
                // Connect existing offline retail customer to user account
                $existingCustomer->update([
                    'user_id' => $user->user_id,
                ]);
            } else {
                // Create customer record if no existing record exists
                Customer::create([
                    'user_id' => $user->user_id,
                    'name' => $registrationData['name'],
                    'mobile' => $registrationData['mobile'],
                ]);
            }

            // Initialize User Cart
            UserCart::create([
                'user_id' => $user->user_id,
                'subtotal' => 0,
                'tax' => 0,
                'shipping' => 0,
                'discount' => 0,
                'total' => 0,
            ]);

            $otpRecord->delete();

            return $user;
        });

        $signedLink = URL::temporarySignedRoute(
            'register.password.form',
            now()->addMinutes(30),
            ['user' => $user->user_id]
        );

        return redirect($signedLink)
            ->withCookie(Cookie::forget('registration_data'))
            ->with('success', 'Mobile verified! Please create your password.');
    }

    public function resendOtp(Request $request)
    {
        $registrationData = json_decode($request->cookie('registration_data'), true);

        if (!$registrationData) {
            return redirect()->route('register.form')
                ->withErrors(['error' => 'Registration session expired. Please start again.']);
        }

        $mobile = $registrationData['mobile'];
        $otpRecord = Otp::where('mobile', $mobile)->first();

        if ($otpRecord && $otpRecord->last_request_at) {
            $secondsSinceLastRequest = Carbon::now()->diffInSeconds($otpRecord->last_request_at);
            if ($secondsSinceLastRequest < self::OTP_COOLDOWN_SECONDS) {
                $retryAfter = self::OTP_COOLDOWN_SECONDS - $secondsSinceLastRequest;
                return back()->withErrors(['error' => "Please wait {$retryAfter} seconds before requesting a new OTP."]);
            }
        }

        $generatedOtp = (string) rand(100000, 999999);

        $smsSent = $this->sendOtpViaApi($mobile, $generatedOtp);

        if (!$smsSent) {
            return back()->withErrors(['error' => 'Failed to resend OTP via SMS. Please try again.']);
        }

        if ($otpRecord) {
            $otpRecord->update([
                'otp' => $generatedOtp,
                'expires_at' => Carbon::now()->addMinutes(self::OTP_EXPIRY_MINUTES),
                'attempts' => $otpRecord->attempts + 1,
                'last_request_at' => Carbon::now(),
                'api_message' => 'OTP resent successfully',
            ]);
        }

        return back()->with('success', 'A new OTP has been dispatched to your mobile number.');
    }

    public function showStepThree(Request $request, User $user)
    {
        if (!$request->hasValidSignature()) {
            abort(401, 'Invalid or expired activation link.');
        }

        if (!$user->exists) {
            return redirect()->route('register.form')
                ->withErrors(['error' => 'User not found. Please register again.']);
        }

        return view('auth.register', [
            'step' => 3,
            'user' => $user
        ]);
    }

    public function processStepThree(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|confirmed|min:6',
        ]);

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        Auth::login($user);

        return redirect()->route('customer.dashboard')
            ->with('success', 'Registration complete! Welcome to our platform.');
    }

    private function sendOtpViaApi(string $mobileNumber, string $code): bool
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => config('services.startmessaging.key'),
            ])->post('https://api.startmessaging.com/otp/send', [
                'phoneNumber' => '+91' . $mobileNumber,
                'templateId'  => config('services.startmessaging.template_id'),
                'variables'   => [
                    'otp'    => $code,
                    'expiry' => (string) self::OTP_EXPIRY_MINUTES,
                ],
            ]);

            if ($response->failed()) {
                Log::error('StartMessaging API failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('SMS Dispatch Exception', ['error' => $e->getMessage()]);
            return false;
        }
    }

    // Login process start:
    public function login()
    {
        return view('auth.login');
    }

    public function signin(Request $request)
    {
        $credentials = $request->validate([
            'login'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $loginType = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';

        $authCredentials = [
            $loginType  => $credentials['login'],
            'password'  => $credentials['password'],
        ];

        $remember = $request->filled('remember');

        if (Auth::attempt($authCredentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->status !== 'active') {
                Auth::logout();
                return back()->withErrors([
                    'status' => 'Your account is inactive. Please contact support.'
                ])->withInput($request->except('password'));
            }

            $sessionIdsToKeep = DB::table('sessions')
                ->where('user_id', $user->user_id)
                ->orderBy('last_activity', 'desc')
                ->limit(2)
                ->pluck('id');

            DB::table('sessions')
                ->where('user_id', $user->user_id)
                ->whereNotIn('id', $sessionIdsToKeep)
                ->delete();

            session()->flash('success', 'Login successful!');

            if ($user->role === 'distributor' && !$user->shop) {
                Log::warning("Distributor {$user->user_id} doesn't have shop information.");
                session()->flash('warning', 'Shop information not found. Please contact support.');
            }

            return match ($user->role) {
                'admin'       => redirect()->route('admin.dashboard'),
                'distributor' => redirect()->route('distributor.dashboard'),
                'customer'    => redirect()->route('customer.dashboard'),
                default       => redirect()->route('index'),
            };
        }

        return back()->withErrors([
            'credentials' => 'The provided credentials do not match our records.',
        ])->withInput($request->except('password'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('index');
    }
}
