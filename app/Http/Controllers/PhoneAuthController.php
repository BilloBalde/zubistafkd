<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PhoneVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class PhoneAuthController extends Controller
{
    // Étape 1 : envoyer le code par SMS
    public function sendCode(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|regex:/^(\+224|224|0)?[0-9]{9}$/'
        ]);

        $phone = $this->formatPhoneNumber($request->phone);
        
        // Générer un code à 6 chiffres
        $code = random_int(100000, 999999);
        $expiresAt = now()->addMinutes(10);

        PhoneVerification::updateOrCreate(
            ['phone' => $phone],
            ['code' => $code, 'expires_at' => $expiresAt]
        );

        // TODO: Appeler votre service SMS (ex: Africa's Talking, Nimba SMS)
        // $this->sendSms($phone, "Votre code de vérification est : $code");

        // En développement, vous pouvez logguer le code ou le retourner
        // ⚠️ À retirer en production
        return response()->json([
            'message' => 'Code envoyé',
            'code' => $code  // retirez cette ligne en prod
        ]);
    }

    // Étape 2 : vérifier le code et connecter/créer l'utilisateur
    public function verifyAndLogin(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'code' => 'required|string|size:6'
        ]);

        $phone = $this->formatPhoneNumber($request->phone);

        $verif = PhoneVerification::where('phone', $phone)
            ->where('code', $request->code)
            ->where('expires_at', '>', now())
            ->first();

        if (!$verif) {
            return back()->withErrors(['code' => 'Code invalide ou expiré']);
        }

        // Récupérer ou créer l'utilisateur
        $user = User::firstOrCreate(
            ['phone' => $phone],
            [
                'name' => 'Client ' . substr($phone, -9),
                'username' => 'u_' . substr($phone, -9) . '_' . uniqid(),
                'email' => 'phone_' . substr($phone, -9) . '_' . uniqid() . '@clients.local',
                'password' => Hash::make(uniqid()),
                'motdepasse' => '',
                'status' => 'Active',
                'token' => '',
                'role_id' => User::ROLE_CUSTOMER,
            ]
        );

        Auth::login($user);
        $verif->delete();

        return redirect()->intended(route('shop.home'));
    }

    /**
     * Formate un numéro de téléphone au format E.164 (+224XXXXXXXXX)
     */
    private function formatPhoneNumber($phone)
    {
        // Supprime tous les espaces, tirets, points
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        
        // Déjà au bon format
        if (preg_match('/^\+224[0-9]{9}$/', $phone)) {
            return $phone;
        }
        
        // Commence par '224' sans le +
        if (preg_match('/^224[0-9]{9}$/', $phone)) {
            return '+' . $phone;
        }
        
        // Commence par 0 (ex: 0771234567)
        if (preg_match('/^0[0-9]{9}$/', $phone)) {
            return '+224' . substr($phone, 1);
        }
        
        // Numéro local à 9 chiffres (ex: 771234567)
        if (preg_match('/^[0-9]{9}$/', $phone)) {
            return '+224' . $phone;
        }
        
        // Fallback : on renvoie tel quel (mais logguez l'erreur)
        return $phone;
    }

    // Déconnexion
    public function logout()
    {
        Auth::logout();
        return redirect()->route('accueil');
    }
}