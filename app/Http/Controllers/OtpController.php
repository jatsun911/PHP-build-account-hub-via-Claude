<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OtpController extends Controller
{
    public function sendEmailOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $otp = 123456; // HARDCODED FOR TESTING
        
        $request->session()->put('otp_email_' . $request->email, $otp);
        $request->session()->put('otp_email_expires_' . $request->email, now()->addMinutes(10));
        
        // Mock sending email
        Log::info("Email OTP for {$request->email}: {$otp}");
        
        return response()->json([
            'status' => 'success', 
            'message' => 'OTP sent to email successfully.',
            // Including OTP in response ONLY for local testing/prototype purposes
            'mock_otp' => $otp 
        ]);
    }

    public function verifyEmailOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric'
        ]);

        $storedOtp = $request->session()->get('otp_email_' . $request->email);
        $expires = $request->session()->get('otp_email_expires_' . $request->email);

        if (!$storedOtp || !$expires || now()->greaterThan($expires)) {
            return response()->json(['status' => 'error', 'message' => 'OTP expired or invalid.'], 400);
        }

        if ($storedOtp == $request->otp) {
            $request->session()->put('email_verified_for_entity', $request->email);
            $request->session()->forget(['otp_email_' . $request->email, 'otp_email_expires_' . $request->email]);
            return response()->json(['status' => 'success', 'message' => 'Email verified successfully.']);
        }

        return response()->json(['status' => 'error', 'message' => 'Invalid OTP.'], 400);
    }

    public function sendMobileOtp(Request $request)
    {
        $request->validate(['mobile' => 'required|string|min:10']);
        $otp = 123456; // HARDCODED FOR TESTING
        
        $request->session()->put('otp_mobile_' . $request->mobile, $otp);
        $request->session()->put('otp_mobile_expires_' . $request->mobile, now()->addMinutes(10));
        
        // Mock sending SMS
        Log::info("Mobile OTP for {$request->mobile}: {$otp}");
        
        return response()->json([
            'status' => 'success', 
            'message' => 'OTP sent to mobile successfully.',
            // Including OTP in response ONLY for local testing/prototype purposes
            'mock_otp' => $otp
        ]);
    }

    public function verifyMobileOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string',
            'otp' => 'required|numeric'
        ]);

        $storedOtp = $request->session()->get('otp_mobile_' . $request->mobile);
        $expires = $request->session()->get('otp_mobile_expires_' . $request->mobile);

        if (!$storedOtp || !$expires || now()->greaterThan($expires)) {
            return response()->json(['status' => 'error', 'message' => 'OTP expired or invalid.'], 400);
        }

        if ($storedOtp == $request->otp) {
            $request->session()->put('mobile_verified_for_entity', $request->mobile);
            $request->session()->forget(['otp_mobile_' . $request->mobile, 'otp_mobile_expires_' . $request->mobile]);
            return response()->json(['status' => 'success', 'message' => 'Mobile verified successfully.']);
        }

        return response()->json(['status' => 'error', 'message' => 'Invalid OTP.'], 400);
    }
}
