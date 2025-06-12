<?php

namespace App\Services;

use App\Models\Otp;
use Carbon\Carbon;

class OtpService
{
    public function generateOtp(string $email): string
    {
        // Generate 6-digit OTP
        $generatedOtp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Delete any existing unused OTPs for this email
        Otp::where('email', $email)->where('is_used', false)->delete();


        $otp = new Otp();
        $otp->email = $email;
        $otp->otp = $generatedOtp;
        $otp->expires_at = Carbon::now()->addMinutes(5); // Set expiry time
        $otp->is_used = false; // Initially not used
        $otp->save();

        return $generatedOtp;
    }

    public function verifyOtp(string $email, string $otp): bool
    {
        $otpRecord = Otp::where('email', $email)
            ->where('is_used', false)
            ->first();

        // Check if OTP exists and is not expired
        if (!$otpRecord || $otpRecord->otp !== $otp) {
            return false;
        }

        if (!$otpRecord || $otpRecord->isExpired()) {
            return false;
        }

        // Mark OTP as used
        $otpRecord->update(['is_used' => true]);

        return true;
    }
}
