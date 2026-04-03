@extends('index')

@section('contain')
    <a href="{{ url('/') }}" class="btn-back-floating">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div class="container register-page">
        <div class="welcome-logo">
            <img src="{{ asset('images/logo epa.jpg.jpeg') }}" alt="Logo EPA">
        </div>
        <div class="login">
            <div class="login-screen">
                <div class="app-title mb-4">
                    <h1>Inscription</h1>
                </div>

                <form action="{{ url('/register-control') }}" method="POST" class="login-form" id="register-form">
                    @csrf
                    <div class="control-group" style="margin-bottom: 5px !important;">
                        <input type="text" name="nom" class="login-field" placeholder="Nom" id="reg-nom" required>
                        <label class="login-field-icon fui-user" for="reg-nom"></label>
                    </div>

                    <div class="control-group" style="margin-bottom: 5px;">
                        <input type="text" name="prenom" class="login-field" placeholder="Prénom" id="reg-prenom" required>
                        <label class="login-field-icon fui-user" for="reg-prenom"></label>
                    </div>

                    <div style="display: flex; gap: 5px; margin-bottom: 5px;">
                        <div class="control-group" style="flex: 1; margin-bottom: 0 !important;">
                            <input type="number" name="age" class="login-field" placeholder="Âge" id="reg-age" required>
                            <label class="login-field-icon fui-user" for="reg-age"></label>
                        </div>

                        <div class="control-group" style="flex: 1; margin-bottom: 0 !important;">
                            <input type="text" name="profession" class="login-field" placeholder="Profession" id="reg-profession" required>
                            <label class="login-field-icon fui-user" for="reg-profession"></label>
                        </div>
                    </div>

                    <div class="control-group" style="display: flex; gap: 8px; align-items: stretch; margin-bottom: 1rem !important;">
                        <div style="display: flex; flex: 1; position: relative;">
                            <input type="text" value="+225" class="login-field" readonly
                                style="width: 50px; border-radius: 10px 0 0 10px; pointer-events: none;">
                            <input type="tel" name="phone" class="login-field" placeholder="Téléphone" id="reg-phone"
                                required pattern="^(?:01|05|07)[0-9]{8}$" inputmode="numeric" maxlength="10"
                                title="Numéro ivoirien à 10 chiffres, commence par 01, 05 ou 07"
                                style="border-radius: 0 10px 10px 0; flex: 1;">
                            <label class="login-field-icon fui-chat" for="reg-phone"></label>
                        </div>
                    </div>

                    <div class="alert alert-info text-start d-none" id="register-status" role="alert"></div>

                    <button type="submit" class="btn btn-large btn-block" id="register-btn">S'enregistrer</button>

                    <div class="partners-logo mt-4">
                        <h6>Nos partenaires : </h6>
                        <div class="d-flex justify-content-center align-items-center gap-3 flex-wrap">
                            <img src="{{ asset('images/partner-1.jpeg') }}" alt="GIMUEMOA" style="height: 40px; object-fit: contain;">
                            <img src="{{ asset('images/partner-2.jpeg') }}" alt="SUNU Assurances" style="height: 40px; object-fit: contain;">
                            <img src="{{ asset('images/partner-3.jpeg') }}" alt="GES-CI" style="height: 40px; object-fit: contain;">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('register-form');
            const registerBtn = document.getElementById('register-btn');
            const statusBox = document.getElementById('register-status');
            const regPhoneInput = document.getElementById('reg-phone');

            function showStatus(message, type = 'info') {
                statusBox.className = `alert alert-${type} text-start`;
                statusBox.textContent = message;
                statusBox.classList.remove('d-none');
            }

            form.addEventListener('submit', function() {
                registerBtn.disabled = true;
                registerBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Traitement...';
            });

            regPhoneInput.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '').slice(0, 10);
                statusBox.classList.add('d-none');
            });
        });
    </script>
@endsection
