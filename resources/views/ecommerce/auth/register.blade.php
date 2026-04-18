@extends('layouts.template')
@section('content')
<div class="content">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Inscription (E-commerce)</div>
                <div class="card-body">
                    <form action="{{ route('otp.register') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label>Nom complet</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Téléphone</label>
                            <input type="text" name="phone" class="form-control" placeholder="77XXXXXXX" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">S'inscrire</button>
                    </form>
                    <hr>
                    <p class="text-center">Déjà un compte ? <a href="{{ route('otp.login') }}">Se connecter</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
