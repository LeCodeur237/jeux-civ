@extends('index')

@section('contain')
    <div class="container">
        <div class="welcome-logo">
            <img src="{{ asset('images/logo epa.jpg.jpeg') }}" alt="Logo EPA">
        </div>
        <div class="login">
            <div class="login-screen">
                <div class="app-title mb-4">
                    <h1>Connexion</h1>
                </div>

                <form action="{{ url('/login-control') }}" method="POST" class="login-form" id="login-form">
                    @csrf
                    <div class="control-group" style="display: flex; position: relative; margin-bottom: 1.4rem !important;">
                        <input type="text" value="+225" class="login-field" readonly style="width: 70px; border-radius: 10px 0 0 10px; pointer-events: none;">
                        <input type="tel" name="phone" class="login-field" placeholder="Numéro de téléphone" id="login-phone" required style="border-radius: 0 10px 10px 0; flex: 1;">
                        <label class="login-field-icon fui-chat" for="login-phone"></label>
                    </div>

                    <div class="control-group mb-4" style="position: relative;">
                        <input type="password" name="password" class="login-field" placeholder="Mot de passe" id="login-pass" required>
                        <i class="bi bi-eye" id="togglePassword" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #777; z-index: 10;"></i>
                    </div>

                    <button type="submit" class="btn btn-large btn-block" id="login-btn">Se connecter</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('login-form').addEventListener('submit', function() {
            const btn = document.getElementById('login-btn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Connexion...';
        });

        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#login-pass');

        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });
    </script>
@endsection
