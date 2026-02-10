@extends('index')

@section('contain')
    <a href="{{ url('/') }}" class="btn-back-floating">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div class="container">
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
                        <input type="text" name="prenom" class="login-field" placeholder="Prénom" id="reg-prenom"
                            required>
                        <label class="login-field-icon fui-user" for="reg-prenom"></label>
                    </div>

                    <div style="display: flex; gap: 5px; margin-bottom: 5px;">
                        <div class="control-group" style="flex: 1; margin-bottom: 0 !important;">
                            <input type="number" name="age" class="login-field" placeholder="Âge" id="reg-age"
                                required>
                            <label class="login-field-icon fui-user" for="reg-age"></label>
                        </div>

                        <div class="control-group" style="flex: 1; margin-bottom: 0 !important;">
                            <input type="text" name="profession" class="login-field" placeholder="Profession"
                                id="reg-profession" required>
                            <label class="login-field-icon fui-user" for="reg-profession"></label>
                        </div>
                    </div>

                    <div class="control-group" style="display: flex; position: relative; margin-bottom: 1.4rem !important;">
                        <input type="text" value="+225" class="login-field" readonly
                            style="width: 70px; border-radius: 10px 0 0 10px; pointer-events: none;">
                        <input type="tel" name="phone" class="login-field" placeholder="Téléphone" id="reg-phone"
                            required style="border-radius: 0 10px 10px 0; flex: 1;">
                        <label class="login-field-icon fui-chat" for="reg-phone"></label>
                    </div>

                    <button type="submit" class="btn btn-large btn-block" id="register-btn">S'enregistrer</button>
                    <div class="partners-logo mt-4">
                        <h6>Nos partenaires : </h6>
                        <div class="d-flex justify-content-center align-items-center gap-3 flex-wrap">
                            <img src="{{ asset('images/partner-1.jpeg') }}" alt="GIMUEMOA"
                                style="height: 40px; object-fit: contain;">
                            <img src="{{ asset('images/partner-2.jpeg') }}" alt="SUNU Assurances"
                                style="height: 40px; object-fit: contain;">
                            <img src="{{ asset('images/partner-3.jpeg') }}" alt="GES-CI"
                                style="height: 40px; object-fit: contain;">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('register-form').addEventListener('submit', function() {
            const btn = document.getElementById('register-btn');
            btn.disabled = true;
            btn.innerHTML =
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Traitement...';
        });
    </script>
@endsection
