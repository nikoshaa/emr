<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Services\OtpService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function page_login()
    {
        if (!Auth::check()) {
            return view('auth.login');
        } else {
            return redirect('/dashboard');
        }
    }

    public function auth(Request $request)
    {
        $credentials = $request->only('phone', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Check if user has email
            if (!$user->email) {
                Auth::logout();
                return redirect('/')->with('gagal', 'Akun Anda belum memiliki email. Silakan hubungi administrator.');
            }

            // Generate OTP
            $otpService = new OtpService();
            $otp = $otpService->generateOtp($user->email);

            // Store user info in session for OTP verification
            Session::put('otp_user_id', $user->id);
            Session::put('otp_user_email', $user->email);

            Mail::send('emails.otp', ['otp' => $otp], function ($message) use ($user) {
                $message->to($user->email);
                $message->subject('Kode OTP untuk Login');
            });

            // Logout user temporarily (they'll be logged in again after OTP verification)
            Auth::logout();

            return response()->json([
                'success' => true,
                'otp' => $otp,
                'email' => $user->email,
                'message' => 'OTP telah dikirim ke email Anda'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No. HP atau password salah'
            ]);
        }
    }
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6'
        ]);

        $userId = Session::get('otp_user_id');
        $userEmail = Session::get('otp_user_email');

        if (!$userId || !$userEmail) {
            return response()->json([
                'success' => false,
                'message' => 'Session telah berakhir. Silakan login kembali.'
            ]);
        }

        $otpService = new OtpService();

        if ($otpService->verifyOtp($userEmail, $request->otp)) {
            // OTP valid, login user
            $user = User::find($userId);
            Auth::login($user);

            // Clear OTP session data
            Session::forget(['otp_user_id', 'otp_user_email']);

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'redirect' => '/dashboard'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP tidak valid atau telah kedaluwarsa'
            ]);
        }
    }
    public function page_register()
    {
        if (!Auth::check()) {
            return view('auth.register');
        } else {
            return redirect('/dashboard');
        }
    }
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:15|unique:users,phone', // Ensure phone is unique in users table
            'password' => 'required|string|min:6|confirmed', // 'confirmed' checks for password_confirmation field
        ]);

        if ($validator->fails()) {
            return redirect()->route('register.page') // Redirect back to register page on validation error
                ->withErrors($validator)
                ->withInput(); // Keep old input
        }

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Hash the password
            'role' => 5, // Default role, adjust if needed
            'status' => 99, // Default status, adjust if needed
            'remember_token' => Str::random(10),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Log the user in immediately after registration
        Auth::login($user);

        return redirect('/dashboard')->with('sukses', 'Registrasi berhasil! Selamat datang.');
    }


    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }

    public function password_baru($id)
    {
        $user = User::find($id);
        // dd($user);
        return view('newpassword', ['user' => $user, 'id' => $id]);
    }
    public function updatepassword(Request $request, $id)
    {
        $this->validate($request, [
            'password' => 'required|min:6',
            'password_konfirm' => 'required_with:password|same:password|min:6'
        ]);

        $password = bcrypt($request->password);
        User::where('id', $id)->update([
            'password' => $password,
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        return redirect()->route('petugas')->with('sukses', 'Selamat, password anda sudah diperbaharui');
    }
}
