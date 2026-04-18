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
                            <img src="{{ asset('assets/img/dksfinal.png') }}" alt="img">
                        </div>
                        <div class="login-userheading">
                            <h4>Entrer votre nouveau mot de passe</h4>
                            @include('layouts.flash')
                        </div>
                        <form action="{{ route('password.update') }}" method="POST">
                            @csrf
                            <div class="form-login">
                                <label>Email</label>
                                <div class="form-addons">
                                    <input name="emailupdate" type="text" value="{{ $email }}" readonly>
                                    <img src="{{ asset('assets/img/icons/mail.svg') }}" alt="img">
                                </div>
                                @error('emailupdate')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Nouveau Mot de Passe</label>
                                    <div class="pass-group">
                                        <input type="password" class=" pass-input" name="password">
                                        <span class="fas toggle-password fa-eye-slash"></span>
                                    </div>
                                </div>
                                @error('password')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Confirm Mot de Passe</label>
                                    <div class="pass-group">
                                        <input type="password" class=" pass-input" name="password_confirmation">
                                        <span class="fas toggle-password fa-eye-slash"></span>
                                    </div>
                                </div>
                                @error('password_confirmation')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-login">
                                <button class="btn btn-login" type="submit">Envoyer</button>
                            </div>
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
