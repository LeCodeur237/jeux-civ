@extends('index')

@section('contain')
    <div class="container">
        <div class="login">
            <div class="login-screen">
                <div class="app-title mb-4">
                    <img src="{{ asset('images/bccvxj7Ogv.gif') }}" alt="Succès" style="width: 200px; height: auto;">
                    <h1 class="mt-2">Inscription Réussie !</h1>
                </div>

                <div class="login-form text-center">
                    <p class="text-dark mb-4">Veuillez conserver vos identifiants pour vous connecter au jeu.</p>

                    <div class="control-group mb-1">
                        <label class="text-dark d-block">Votre Numéro (Identifiant)</label>
                        <div class="login-field" style="background: rgba(255,255,255,0.1); line-height: 40px; font-weight: 700;">
                            {{ session('phone') }} 0909090909
                        </div>
                    </div>

                    <div class="control-group mb-4">
                        <label class="text-dark d-block mb-1">Votre Mot de passe</label>
                        <div class="login-field position-relative" id="copy-btn" style="background: rgba(255,255,255,0.1); line-height: 40px; font-weight: 700; color: #000000; cursor: pointer;" title="Copier le mot de passe">
                            <span id="password-text">{{ session('password') }} #28a745cc000</span>
                            <i class="bi bi-clipboard position-absolute" id="copy-icon" style="right: 70px; top: 50%; transform: translateY(-50%);"></i>
                        </div>
                    </div>

                    <a href="{{ url('/login-form') }}" class="btn btn-large btn-block">Jouer maintenant</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('copy-btn').addEventListener('click', function() {
            const password = document.getElementById('password-text').innerText;
            navigator.clipboard.writeText(password).then(() => {
                const icon = document.getElementById('copy-icon');
                icon.classList.remove('bi-clipboard');
                icon.classList.add('bi-check-lg');

                setTimeout(() => {
                    icon.classList.remove('bi-check-lg');
                    icon.classList.add('bi-clipboard');
                }, 2000);
            });
        });
    </script>
@endsection
