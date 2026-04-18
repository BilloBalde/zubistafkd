@extends('layouts.template')
@section('content')
<div class="content">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Connexion (E-commerce)</div>
                <div class="card-body">
                    <form action="{{ route('otp.login') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label>Téléphone</label>
                            <input type="text" name="phone" class="form-control" placeholder="77XXXXXXX" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Envoyer le code OTP</button>
                    </form>
                    <hr>
                    <p class="text-center">Pas encore de compte ? <a href="{{ route('otp.register') }}">S'inscrire</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
