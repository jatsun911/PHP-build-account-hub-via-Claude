<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use PragmaRX\Google2FA\Google2FA;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            if (!$user->google2fa_secret) {
                // 2FA not enabled, login directly
                Auth::login($user);
                $request->session()->regenerate();
                return redirect()->intended('/');
            }
            
            // Password is correct, 2FA enabled, proceed to verification
            $request->session()->put('2fa_user_id', $user->id);
            return redirect()->route('2fa.verify');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'serial' => $this->generateSerial(),
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $request->session()->put('2fa_user_id', $user->id);
        return redirect()->route('2fa.setup');
    }

    private function generateSerial()
    {
        $count = \Illuminate\Support\Facades\DB::table('users')->count() + 1;
        $randomString = strtoupper(\Illuminate\Support\Str::random(10));
        return "{$count}.{$randomString}";
    }

    public function show2faSetup(Request $request)
    {
        $userId = $request->session()->get('2fa_user_id');
        if (!$userId) return redirect()->route('login');

        $user = User::find($userId);
        $google2fa = new Google2FA();
        
        if (!$user->google2fa_secret) {
            $user->google2fa_secret = $google2fa->generateSecretKey();
            $user->save();
        }

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            'AccountHub',
            $user->email,
            $user->google2fa_secret
        );
        
        $writer = new \BaconQrCode\Writer(new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(250),
            new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
        ));
        $qrCodeSvg = $writer->writeString($qrCodeUrl);

        return view('auth.2fa_setup', ['qrCodeSvg' => $qrCodeSvg, 'secret' => $user->google2fa_secret]);
    }

    public function skip2fa(Request $request)
    {
        $userId = $request->session()->get('2fa_user_id');
        if (!$userId) return redirect()->route('login');

        $user = User::find($userId);
        $user->google2fa_secret = null; // Ensure it is null
        $user->save();

        Auth::login($user);
        $request->session()->forget('2fa_user_id');
        $request->session()->regenerate();
        
        return redirect()->intended('/');
    }

    public function show2faVerify(Request $request)
    {
        if (!$request->session()->has('2fa_user_id')) return redirect()->route('login');
        return view('auth.2fa_verify');
    }

    public function verify2fa(Request $request)
    {
        $request->validate(['one_time_password' => 'required']);
        
        $userId = $request->session()->get('2fa_user_id');
        if (!$userId) return redirect()->route('login');
        
        $user = User::find($userId);
        $google2fa = new Google2FA();
        
        $valid = $google2fa->verifyKey($user->google2fa_secret, $request->one_time_password);
        
        if ($valid) {
            Auth::login($user);
            $request->session()->forget('2fa_user_id');
            $request->session()->regenerate();
            return redirect()->intended('/');
        }
        
        return back()->withErrors(['one_time_password' => 'Invalid OTP code']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
