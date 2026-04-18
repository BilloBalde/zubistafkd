<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\View\View
     */
    public function showLinkRequestForm()
    {
        return view('users.forgot_password');
    }

    public function showResetForm($token, $email){
        return view('auth.passwords.email', compact('token', 'email'));
    }

    /**
     * Handle a password reset link request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function sendResetLinkEmail(Request $request)
    {
        // Validate the email address
        $request->validate(['email' => 'required|email']);

        // Attempt to send the password reset email
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // Handle the response
        if ($status === Password::RESET_LINK_SENT) {
            //dd('Je tombe ici');
            return redirect()->back()->with('success', 'Nous avons envoyer une email de recuperation de mot de passe');
        }

        // Handle the case where the email was not sent
        throw ValidationException::withMessages([
            'error' => [trans($status)],
        ]);
    }

    public function reset(Request $request){
        $request->validate([
            'emailupdate' => 'required|email',
            'password' => 'required|string|min:8|max:16',
            'password_confirmation' => 'required|string|min:8|max:16'
        ],[
            'emailupdate.required' => 'Le champ emailupdate doit être rempli',
            'emailupdate.email' => 'Le champ emailupdate doit être un email valid avec @ et .',
            'password.required' => 'Vous devez obligatoirement remplir le nouveau mot de passe',
            'password.string' => 'Le champ nouveau mot de passe doit être une chaine de caractère',
            'password.min' => 'Le champ nouveau mot de passe doit prendre minimum 8 caractères',
            'password.max' => 'Le champ nouveau mot de passe doit prendre maximum 16 caractères',
            'password_confirmation.required' => 'Vous devez obligatoirement remplir le mot de passe confirmation',
        ]);
        $email = $request->emailupdate;
        $newpassword = $request->password;
        //dd($email, $newpassword);
        $userExist = User::where('email', $email)->first();
        if($userExist != NULL){
            $user = User::where('email', $email)->first();
            $user->password = Hash::make($newpassword);
            try {
                $user->update();
                return redirect()->route('login')->with('success', 'Votre profil a été mis à jour. Veuillez vous connecter à nouveau.');
            } catch (\Exception $e) {
                    // Handle any errors during the email sending process
                return redirect()->route('login')->with('success', 'Votre profil a été mis à jour, mais l\'email de confirmation n\'a pas pu être envoyé. Veuillez vous connecter à nouveau.');
            }
        }else{
            return back()->with('fall', "modification de profile non réussie, email ou mot de passe erreur.");
        }
    }
}
