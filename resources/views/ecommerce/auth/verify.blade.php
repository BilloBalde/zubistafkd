@extends('layouts.template')
@section('content')
<div class="content">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body p-5">
                    <h3 class="text-center mb-4">Vérification OTP</h3>
                    <p class="text-center text-muted mb-4">Veuillez entrer le code envoyé au <strong>{{ $phone }}</strong></p>
                    
                    <form action="{{ route('otp.verify_submit') }}" method="POST">
                        @csrf
                        <input type="hidden" name="phone" value="{{ $phone }}">
                        <div class="form-group mb-4">
                            <input type="text" name="code" class="form-control text-center display-4" 
                                   maxlength="6" placeholder="000000" autofocus required
                                   style="letter-spacing: 0.5rem; font-weight: bold;">
                        </div>
                        <button type="submit" class="btn btn-warning btn-lg w-100 font-weight-bold">Vérifier le code</button>
                    </form>
                    
                    <div class="text-center mt-4">
                        <small>Vous n'avez pas reçu le code ? <a href="#" class="text-warning">Renvoyer</a></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
