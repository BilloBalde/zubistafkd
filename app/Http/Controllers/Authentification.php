<?php

namespace App\Http\Controllers;

use App\Mail\PasswordMail;
use App\Mail\UserMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use tidy;

class Authentification extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            return Auth::user()->isCustomer()
                ? redirect()->route('shop.home')
                : redirect()->route('home');
        }

        return view('users.login');
    }


    public function forgotPass(){
        return view('users.forgot_password');
    }

    public function register(){
        return view('users.register');
    }

    protected function create(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'username' => 'required|string',
            'password' => 'required|string|min:8|max:16',
            'password_confirmation' => 'required|string|min:8|max:16',
            'phone' => 'required|string|max:20',
            'name' => 'required|string|max:255'
        ]);
        if(request()->hasfile('profilePic')){
            $avatarName = time().'.'.request()->profilePic->getClientOriginalExtension();
            request()->profilePic->move(public_path('avatars'), $avatarName);
        }
        $token = hash('sha256', time());
        try{
            $user=   User::create([
                'name' => $request->name,
                'username' => $request->username,
                'phone' => $request->phone,
                'role_id' => 3,
                'email' => $request->email,
                'profilePic' => '1725478994.png',
                'status' => 'pending',
                'token' => $token,
                'description' => $request->description ?? null,
                'password' => Hash::make($request->password),
                'motdepasse' => $request->password ?? 0,
            ]);
        


            $verification_link = url('registration/verification/'.$token.'/'.$request->email);
            $subject = 'Confirmation request';
            $message = 'Veuillez cliquer sur ce lien de confirmation pour valider votre compte <br> <a href='.$verification_link.'>'.$verification_link.'</a>';

            Mail::to($request->email)->send(new UserMail($subject, $message));
            return back()->with('success', 'Utilisateur crée avec succès.');

        }
        catch(\Exception $e) {
            return back()->with('fall', 'une erreur lors de lajout, voici le message : '.$e->getMessage());
        }
    }

       public function edit($id){
        $user = User::find($id);
        return view('users.edit', compact('user'));
    }
     public function update(Request $request, $id)
    {
        $user = User::find($id);
        
        // Store whether this is the current user BEFORE any changes
        $isCurrentUser = Auth::check() && Auth::id() == $user->id;
        
        $user->name = $request->name;
        $user->username = $request->username;
        $user->phone = $request->phone;
        $user->email = $request->email;
        
        if ($request->password != NULL) {
            $user->password = Hash::make($request->password);
            $passwordChanged = true;
        } else {
            $passwordChanged = false;
        }
        
        try {
            $user->update();
            
            // Get user name for personalization
            $userName = $user->name ?? $user->username ?? 'Utilisateur';
            
            // Send password change notification
            if (!empty($user->email) && $passwordChanged) {
                Mail::to($user->email)->send(new PasswordMail(
                    '🔐 Modification de votre mot de passe - EDAAG TRADING',
                    'Votre mot de passe a été modifié avec succès.',
                    $userName,
                    'changed'
                ));
            }
            
            // If this is the current user and password was changed, logout
            if ($isCurrentUser && $passwordChanged) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('accueil')->with('success', 'Votre profil a été mis à jour. Veuillez vous reconnecter.');
            }
            
            // Otherwise just redirect back with success
            return redirect()->back()->with('success', 'Utilisateur modifié avec succès.');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Une erreur est survenue: '.$e->getMessage());
        }
    }
    public function destroy($id)
{
    // Ensure the user with this ID exists
    $user = User::find($id);

    if (!$user) {
        return redirect()->back()->with('error', 'Utilisateur non trouvé.');
    }

    try {
        // Delete the user
        $user->delete();

        // Redirect back with success message
        return redirect()->route('users.index')->with('success', 'Utilisateur supprimé avec succès.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Une erreur est survenue: ' . $e->getMessage());
    }
}
    public function registration_verify($token, $email){
        $userExist = User::where('email', $email)->where('token', $token)->first();
        if(!$userExist){
            return redirect()->route('login')->with('fall', 'Utilisateur déjà activé ou n\'existe pas.');
        }else{
            $userExist->status = 'Active';
            $userExist->token = '';
            $userExist->update();
            return redirect()->route('login')->with('success', 'Utilisateur activé avec succès.');
        }

    }

    public function passwordRecovery(Request $request){
        //dd($request->email);
        $existUser = User::where('email',$request->email)->first();
        if ($existUser == NULL) {
            return back()->with('fall', 'User exists not');
        }
        $subject = 'Recuperation de Mot de Passe';//Auth::user()->getAuthPassword()
        $message = 'Here is your password : '.$existUser->password;

        Mail::to($request->email)->send(new PasswordMail($subject, $message));
        return redirect()->route('login')->with('success', 'Verifier votre email pour voir votre mot de passe.');
    }

    public function login_submit(Request $request){
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
            'status' => "Active"
        ];

        //dd(Auth::attempt($credentials));

        if (Auth::attempt($credentials)) {
            $user = User::where('email', $request->email)->first();
            if ($user && $user->isCustomer()) {
                return redirect()->route('shop.home');
            }

            return redirect()->route('home');
        } else {
            return back()->with('fall', 'Utilisateur n\'existe pas');
        }
    }

    public function sendEmail(Request $request){
        $validated = $request->validate([
            'email' => 'required|email',
            'name' => 'required|string',
            'subject' => 'required|string',
            'message' => 'required|string'
        ]);
        /* ,[
            'email.required' => 'Vous devez remplir le champ email',
            'email.email' => 'Le champ email doit contenir un @ et .',
            'name.required' => 'Vous devez remplir le champ name',
            'name.string' => 'Le champ name ne prend que des chaines de caracteres',
            'subject.required' => 'Vous devez remplir le champ subject',
            'subject.string' => 'Le champ subject ne prend que des chaines de caracteres',
            'message.required' => 'Vous devez remplir le champ message',
            'message.string' => 'Le champ message ne prend que des chaines de caracteres',
        ] */

        try {
            Mail::to('contact@dksamadou.com')->send(new UserMail($request->subject,
            'Email envoyeur: '.$request->email.'\nNom envoyeur: '.$request->name.'\nMessage: '.$request->message
        ));
            /* Mail::send([], [], function ($message) use ($request) {
                $message->to('contact@dksamadou.com') // Replace with your recipient
                        ->subject($request->subject)
                        ->setBody($request->message, 'text/html'); // You can send HTML content

                $message->from($request->email, $request->name);
            }); */
            return redirect()->back()->with('success', 'Votre mail a ete envoye avec succes.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Erreur lors de envoi de email'.$th->getMessage());
        }
    }

}
