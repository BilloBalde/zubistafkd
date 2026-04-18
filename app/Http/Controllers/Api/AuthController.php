<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users,phone',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $this->authService->register($request->only('name', 'phone'));
        $otp = $this->authService->generateOtp($user->phone);

        return response()->json([
            'message' => 'Inscription réussie. Code OTP envoyé.',
            'phone' => $user->phone,
            'otp' => $otp->code // En prod, ne pas renvoyer le code, l'envoyer par SMS
        ]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $this->authService->login($request->phone);

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé'], 404);
        }

        $otp = $this->authService->generateOtp($user->phone);

        return response()->json([
            'message' => 'Code OTP envoyé.',
            'phone' => $user->phone,
            'otp' => $otp->code
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $verified = $this->authService->verifyOtp($request->phone, $request->code);

        if (!$verified) {
            return response()->json(['message' => 'OTP invalide ou expiré'], 401);
        }

        $user = $this->authService->login($request->phone);
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Vérification réussie',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }
}
