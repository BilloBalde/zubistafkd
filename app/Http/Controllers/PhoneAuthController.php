<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PhoneVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class PhoneAuthController extends Controller
{
    // Étape 1 : demander le code
    public function sendCode(Request $request)
    {
        $request->validate(['phone' => 'required|string']);
        $phone = $request->phone;

        // Générer un code à 6 chiffres
        $code = random_int(100000, 999999);
        $expiresAt = now()->addMinutes(10);

        PhoneVerification::updateOrCreate(
            ['phone' => $phone],
            ['code' => $code, 'expires_at' => $expiresAt]
        );

        // Envoyer le SMS via un service (ex: Twilio, Afromessage, etc.)
        // $this->sendSms($phone, "Votre code FBK est : $code");

        // Pour le développement, on renvoie le code en JSON (à enlever en prod)
        return response()->json(['message' => 'Code envoyé', 'code' => $code]);
    }

    // Étape 2 : vérifier et connecter
    public function verifyAndLogin(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'code' => 'required|string|size:6'
        ]);

        $verif = PhoneVerification::where('phone', $request->phone)
            ->where('code', $request->code)
            ->where('expires_at', '>', now())
            ->first();

        if (!$verif) {
            return back()->withErrors(['code' => 'Code invalide ou expiré']);
        }

        // Créer ou récupérer l'utilisateur
        $user = User::firstOrCreate(
            ['phone' => $request->phone],
            ['name' => substr($request->phone, -4), 'password' => Hash::make(uniqid())]
        );

        $user->update(['phone_verified_at' => now()]);
        Auth::login($user);
        $verif->delete();

        return redirect()->intended('/');
    }

    // Déconnexion
    public function logout()
    {
        Auth::logout();
        return redirect()->route('accueil');
    }
}