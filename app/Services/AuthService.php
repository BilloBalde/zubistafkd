<?php

namespace App\Services;

use App\Models\User;
use App\Models\OtpCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService
{
    public function generateOtp($phone)
    {
        // Supprimer les anciens OTP non expirés pour ce numéro
        OtpCode::where('phone', $phone)->where('verified', false)->delete();

        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        return OtpCode::create([
            'phone' => $phone,
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes(5),
            'verified' => false
        ]);
    }

    public function verifyOtp($phone, $code)
    {
        $otp = OtpCode::where('phone', $phone)
            ->where('code', $code)
            ->where('verified', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if ($otp) {
            $otp->update(['verified' => true]);
            return true;
        }

        return false;
    }

    public function register($data)
    {
        return User::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'role_id' => 2, // Client par défaut
            'password' => Hash::make(Str::random(16)), // Mot de passe aléatoire car Auth par OTP
            'status' => 'active'
        ]);
    }

    public function login($phone)
    {
        return User::where('phone', $phone)->first();
    }
}
