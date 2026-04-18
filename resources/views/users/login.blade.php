<!DOCTYPE html>
<html lang="en">
    @include('layouts.head')
    <body class="account-page">

    <div class="main-wrapper">
        <div class="account-content">
            <div class="login-wrapper">
                <div class="login-content">
                    <div class="login-userset">
                        <div class="login-logo">
                            <img src="{{ asset('companies/'.App\Models\Company::latest()->first()->logo) }}" alt="img">
                        </div>
                        <div class="login-userheading">
                            <h3>Se Connecter</h3>
                            <h4>Veuillez vous connecter à votre compte</h4>
                        </div>
                        @include('layouts.flash')
                        <form action="{{ route('login_submit') }}" method="post">
                            @csrf
                            <div class="form-login">
                                <label>Email</label>
                                <div class="form-addons">
                                    <input type="text" name="email" placeholder="Enter your email address">
                                    <img src="{{ asset('assets/img/icons/mail.svg') }}" alt="img">
                                </div>
                            </div>
                            <div class="form-login">
                                <label>Mot de Passe</label>
                                <div class="pass-group">
                                    <input type="password" name="password" class="pass-input" placeholder="Enter your password">
                                    <span class="fas toggle-password fa-eye-slash"></span>
                                </div>
                            </div>
                            <div class="form-login">
                                <div class="alreadyuser">
                                    <h4><a href="{{ route('forgotPass') }}" class="hover-a">Mot de Passe Oublié?</a></h4>
                                </div>
                            </div>
                            <div class="form-login">
                                <button class="btn btn-login" type="submit">Se Connecter</button>
                            </div>
                            <a href="{{ route('accueil') }}">Retour à la page d'accueil</a>
                        </form>
                    </div>
                </div>
                <div class="login-img">
                    <img src="{{ asset('assets/img/login.jpg') }}" alt="img">
                </div>
            </div>
        </div>
    </div>

    @include('layouts.scripts')
</body>
</html>
