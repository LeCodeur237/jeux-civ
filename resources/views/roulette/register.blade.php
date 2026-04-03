@extends('index')

@section('contain')
    @php
        $firebaseTestMode = (bool) config('services.firebase.phone_auth_test_mode');
        $firebaseTestCode = config('services.firebase.phone_auth_test_code') ?: '123456';
    @endphp
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
                    @if($firebaseTestMode)
                        <small class="d-block mt-2 text-muted">Mode test gratuit activé</small>
                    @endif
                </div>

                <form action="{{ url('/register-control') }}" method="POST" class="login-form" id="register-form">
                    @csrf
                    <input type="hidden" name="firebase_verified" id="firebase-verified" value="0">
                    <input type="hidden" name="firebase_phone" id="firebase-phone" value="">

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
                        <button type="button" class="btn btn-secondary" id="send-code-btn"
                            style="width: auto; min-width: 100px; white-space: nowrap;">
                            Envoyer
                        </button>
                    </div>

                    <div class="text-start mb-3">
                        <div id="recaptcha-container"></div>
                    </div>

                    <div class="alert alert-info text-start d-none" id="firebase-status" role="alert"></div>

                    <button type="submit" class="btn btn-large btn-block" id="register-btn" disabled>S'enregistrer</button>

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

    <div class="modal fade" id="verificationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Vérification Firebase</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">Entrez le code reçu par SMS pour vérifier votre numéro.</p>
                    <div class="control-group mb-3">
                        <input type="text" class="login-field" placeholder="Code de vérification" id="verification-code" maxlength="6" inputmode="numeric">
                        <label class="login-field-icon fui-lock" for="verification-code"></label>
                    </div>
                    <div class="alert alert-info d-none" id="verification-status" role="alert"></div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" id="verify-code-btn">Vérifier le code</button>
                </div>
            </div>
        </div>
    </div>

    <script type="module">
        const firebaseTestMode = @json($firebaseTestMode);
        const firebaseTestCode = @json($firebaseTestCode);

        let initializeApp = null;
        let getAuth = null;
        let RecaptchaVerifier = null;
        let signInWithPhoneNumber = null;
        let firebaseReady = false;
        let app = null;
        let auth = null;

        if (!firebaseTestMode) {
            const firebaseAppModule = await import("https://www.gstatic.com/firebasejs/10.12.5/firebase-app.js");
            const firebaseAuthModule = await import("https://www.gstatic.com/firebasejs/10.12.5/firebase-auth.js");

            initializeApp = firebaseAppModule.initializeApp;
            getAuth = firebaseAuthModule.getAuth;
            RecaptchaVerifier = firebaseAuthModule.RecaptchaVerifier;
            signInWithPhoneNumber = firebaseAuthModule.signInWithPhoneNumber;

            const firebaseConfig = {
                apiKey: @json(config('services.firebase.api_key')),
                authDomain: @json(config('services.firebase.auth_domain')),
                projectId: @json(config('services.firebase.project_id')),
                storageBucket: @json(config('services.firebase.storage_bucket')),
                messagingSenderId: @json(config('services.firebase.messaging_sender_id')),
                appId: @json(config('services.firebase.app_id')),
            };

            firebaseReady = Object.values(firebaseConfig).every(Boolean);
            app = firebaseReady ? initializeApp(firebaseConfig) : null;
            auth = firebaseReady ? getAuth(app) : null;
        } else {
            firebaseReady = true;
        }

        const form = document.getElementById('register-form');
        const sendCodeBtn = document.getElementById('send-code-btn');
        const verifyCodeBtn = document.getElementById('verify-code-btn');
        const registerBtn = document.getElementById('register-btn');
        const statusBox = document.getElementById('firebase-status');
        const verificationCode = document.getElementById('verification-code');
        const verificationStatus = document.getElementById('verification-status');
        const firebaseVerified = document.getElementById('firebase-verified');
        const firebasePhone = document.getElementById('firebase-phone');
        const regPhoneInput = document.getElementById('reg-phone');
        const verificationModalEl = document.getElementById('verificationModal');
        const verificationModal = new bootstrap.Modal(verificationModalEl);

        let confirmationResult = null;
        let recaptchaVerifier = null;
        let recaptchaReady = false;

        function showStatus(message, type = 'info') {
            statusBox.className = `alert alert-${type} text-start`;
            statusBox.textContent = message;
            statusBox.classList.remove('d-none');
        }

        function showVerificationStatus(message, type = 'info') {
            verificationStatus.className = `alert alert-${type}`;
            verificationStatus.textContent = message;
            verificationStatus.classList.remove('d-none');
        }

        function hideStatus() {
            statusBox.classList.add('d-none');
            statusBox.textContent = '';
        }

        function hideVerificationStatus() {
            verificationStatus.classList.add('d-none');
            verificationStatus.textContent = '';
        }

        verificationModalEl.addEventListener('hidden.bs.modal', function() {
            verificationCode.value = '';
            hideVerificationStatus();
            if (firebaseVerified.value !== '1') {
                confirmationResult = null;
            }
        });

        function resetRecaptcha() {
            try {
                if (recaptchaVerifier && typeof recaptchaVerifier.clear === 'function') {
                    recaptchaVerifier.clear();
                }
            } catch (error) {
                console.warn('Unable to clear reCAPTCHA verifier:', error);
            }

            recaptchaVerifier = null;
            recaptchaReady = false;
        }

        if (!firebaseTestMode && !firebaseReady) {
            sendCodeBtn.disabled = true;
            verifyCodeBtn.disabled = true;
            registerBtn.disabled = true;
            showStatus('Firebase n’est pas configuré. Renseignez les variables FIREBASE_* dans le fichier .env.', 'warning');
        }

        function normalizePhone(value) {
            const digits = (value || '').replace(/\D/g, '');
            if (digits.length === 10 && /^(01|05|07)/.test(digits)) {
                return `+225${digits}`;
            }
            if (digits.length === 13 && digits.startsWith('225')) {
                const local = digits.slice(3);
                if (/^(01|05|07)[0-9]{8}$/.test(local)) {
                    return `+225${local}`;
                }
            }
            return null;
        }

        function ensureRecaptcha() {
            if (recaptchaVerifier) {
                return recaptchaVerifier;
            }

            recaptchaVerifier = new RecaptchaVerifier(auth, 'recaptcha-container', {
                size: 'invisible'
            });

            return recaptchaVerifier;
        }

        async function sendCode() {
            if (!firebaseTestMode && !firebaseReady) {
                return;
            }

            const phone = normalizePhone(regPhoneInput.value);

            if (!phone) {
                showStatus('Le numéro doit être ivoirien et commencer par 01, 05 ou 07.', 'warning');
                return;
            }

            hideStatus();
            sendCodeBtn.disabled = true;
            sendCodeBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Envoi...';

            try {
                if (firebaseTestMode) {
                    confirmationResult = { phone, code: firebaseTestCode };
                    recaptchaReady = true;
                    firebasePhone.value = phone;
                    verificationCode.value = '';
                    hideVerificationStatus();
                    showVerificationStatus(`Mode test: utilisez le code ${firebaseTestCode}`, 'info');
                    verificationModal.show();
                    setTimeout(() => verificationCode.focus(), 150);
                } else {
                    if (!recaptchaReady) {
                        resetRecaptcha();
                    }

                    const verifier = ensureRecaptcha();
                    confirmationResult = await signInWithPhoneNumber(auth, phone, verifier);
                    recaptchaReady = true;
                    firebasePhone.value = phone;
                    verificationCode.value = '';
                    hideVerificationStatus();
                    verificationModal.show();
                    setTimeout(() => verificationCode.focus(), 150);
                    showStatus('Code envoyé par SMS. Entrez le code reçu pour continuer.', 'success');
                }
                if (firebaseTestMode) {
                    showStatus('Mode test: entrez le code affiché dans la fenêtre.', 'success');
                } else {
                    showStatus('Code envoyé par SMS. Entrez le code reçu pour continuer.', 'success');
                }
            } catch (error) {
                console.error(error);
                resetRecaptcha();
                confirmationResult = null;
                firebaseVerified.value = '0';
                registerBtn.disabled = true;
                showStatus(error.message || 'Impossible d’envoyer le code SMS.', 'danger');
            } finally {
                sendCodeBtn.disabled = false;
                sendCodeBtn.textContent = 'Envoyer';
            }
        }

        async function verifyCode() {
            if (!firebaseTestMode && !firebaseReady) {
                return;
            }

            const code = verificationCode.value.trim();

            if (!confirmationResult) {
                showStatus('Veuillez d’abord envoyer le code SMS.', 'warning');
                return;
            }

            if (code.length < 6) {
                showStatus('Le code doit contenir 6 chiffres.', 'warning');
                return;
            }

            verifyCodeBtn.disabled = true;
            verifyCodeBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Vérification...';

            try {
                if (firebaseTestMode) {
                    if (code !== String(confirmationResult.code)) {
                        throw new Error('Code invalide.');
                    }
                } else {
                    await confirmationResult.confirm(code);
                }
                firebaseVerified.value = '1';
                registerBtn.disabled = false;
                showStatus('Numéro vérifié avec succès. Vous pouvez maintenant vous inscrire.', 'success');
                hideVerificationStatus();
                verificationModal.hide();
            } catch (error) {
                console.error(error);
                firebaseVerified.value = '0';
                registerBtn.disabled = true;
                confirmationResult = null;
                resetRecaptcha();
                showVerificationStatus(error.message || 'Code invalide.', 'danger');
                verificationCode.value = '';
            } finally {
                verifyCodeBtn.disabled = false;
                verifyCodeBtn.textContent = 'Vérifier le code';
            }
        }

        sendCodeBtn.addEventListener('click', sendCode);
        verifyCodeBtn.addEventListener('click', verifyCode);

        form.addEventListener('submit', function(event) {
            if (firebaseVerified.value !== '1') {
                event.preventDefault();
                showStatus('Veuillez d’abord vérifier votre numéro par SMS.', 'warning');
                return;
            }

            const btn = document.getElementById('register-btn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Traitement...';
        });

        regPhoneInput.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '').slice(0, 10);
            firebaseVerified.value = '0';
            registerBtn.disabled = true;
            confirmationResult = null;
            resetRecaptcha();
            verificationCode.value = '';
            hideVerificationStatus();
            hideStatus();
        });
    </script>
@endsection
