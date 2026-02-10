@extends('index')

@section('contain')
    <div class="valentine-register"
        style="background-image: linear-gradient(180deg, rgba(63, 0, 8, 0.92), rgba(20, 0, 4, 0.75)), url('{{ asset('images/51941.jpg') }}');">
        <img class="valentine-decor" src="{{ asset('img/coeur.png') }}" alt="" aria-hidden="true">
        <div class="container valentine-layout">
            <div class="valentine-header">
                <a href="{{ url('/') }}" class="btn-back-floating">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div class="app-title">
                    <h1>Inscription</h1>
                </div>
            </div>

            <div class="login valentine-card">
                <div class="login-screen">
                    <form class="login-form" id="register-form">
                        @csrf
                        <div class="control-group" style="margin-bottom: 5px !important;">
                            <input type="text" name="nom" class="login-field" placeholder="Nom" id="reg-nom"
                                required>
                            <label class="login-field-icon fui-user" for="reg-nom"></label>
                        </div>

                        <div class="control-group" style="margin-bottom: 5px;">
                            <input type="text" name="prenom" class="login-field" placeholder="Prénom" id="reg-prenom"
                                required>
                            <label class="login-field-icon fui-user" for="reg-prenom"></label>
                        </div>

                    <div class="control-group"
                        style="display: flex; position: relative; margin-bottom: 1.4rem !important;">
                        <input type="text" value="+225" class="login-field" readonly
                            style="width: 70px; border-radius: 10px 0 0 10px; pointer-events: none;">
                        <input type="tel" name="telephone_input" class="login-field" placeholder="Téléphone" id="reg-phone"
                            required style="border-radius: 0 10px 10px 0; flex: 1;">
                        <label class="login-field-icon fui-chat" for="reg-phone"></label>
                    </div>

                    <div class="rgpd-row">
                        <input type="checkbox" id="rgpd-consent" name="is_accept" required>
                        <label for="rgpd-consent">
                            J'accepte la
                            <a href="#" class="rgpd-link" data-bs-toggle="modal" data-bs-target="#rgpdModal">
                                politique de confidentialité
                            </a>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-large btn-block valentine-btn"
                        id="register-btn">S'enregistrer</button>
                    </form>
                </div>
            </div>

            <div class="lots-row" aria-label="Lots a gagner">
                <div class="lots-row-text">
                    Bonne chance 😍
                </div>
            </div>
        </div>

    <script>
        function showSnackbar(message) {
            var x = document.getElementById("snackbar");
            x.innerText = message;
            x.className = "show";
            setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
        }

        document.getElementById('register-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const btn = document.getElementById('register-btn');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML =
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Traitement...';

            const nom = document.getElementById('reg-nom').value;
            const prenom = document.getElementById('reg-prenom').value;
            const phoneInput = document.getElementById('reg-phone').value;
            // On concatène l'indicatif +225
            const telephone = '+225' + phoneInput;
            const isAccept = document.getElementById('rgpd-consent').checked;
            const token = document.querySelector('input[name="_token"]').value;

            const formData = new FormData();
            formData.append('nom', nom);
            formData.append('prenom', prenom);
            formData.append('telephone', telephone);
            formData.append('is_accept', isAccept ? '1' : '0');
            formData.append('_token', token);

            fetch("{{ url('/register-player') }}", {
                method: 'POST',
                headers: {
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    localStorage.setItem('player_id', data.player.id);
                    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();
                    setTimeout(() => {
                        window.location.href = "{{ url('/valentines-day') }}";
                    }, 5000);
                } else {
                    showSnackbar(data.message || 'Une erreur est survenue. Vérifiez vos informations (téléphone unique).');
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showSnackbar('Une erreur technique est survenue.');
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        });
    </script>

    <div class="modal fade valentine-modal" id="rgpdModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Politique de confidentialité</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body rgpd-text">
                    <p>Nous collectons vos informations (nom, prénom et numéro de téléphone) uniquement pour
                        l'organisation du jeu et la remise des lots. Vos données ne seront ni vendues ni partagées avec
                        des tiers non autorisés. Vous pouvez demander leur suppression à tout moment.</p>
                    <p>En cochant la case, vous acceptez cette politique de confidentialité.</p>
                    <br>
                    <p>Pour incompréhension ou difficulté, veuillez nous contacter à l'adresse mail : <a href="mailto:contact@diversitypub.com">contact@diversitypub.com</a></p>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade valentine-modal" id="successModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Inscription réussie</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body rgpd-text text-center">
                    <img src="{{ asset('images/succes.gif') }}" alt="Succès" class="img-fluid mb-3"
                        style="max-height: 160px;">
                    <p>Votre inscription a été enregistrée avec succès. veuillez patienter...</p>
                </div>
            </div>
        </div>
    </div>
@endsection
