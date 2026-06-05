@extends('index')
@section('contain')

<a href="{{ url('/') }}" class="btn-back-floating" aria-label="Retour">
    <i class="bi bi-arrow-left"></i>
</a>

<div class="register-page-wrapper">
  <div class="login">

    <div class="login-hero">
      <div class="login-hero-avatar">
        <i class="bi bi-person-plus" style="font-size: 1.8rem; color: #fff;"></i>
      </div>
      <h1>Créer un compte</h1>
      <p>Rejoignez EPA en quelques secondes</p>
    </div>

    <div class="login-screen">
      <form action="{{ url('/register-control') }}" method="POST" class="login-form" id="register-form">
        @csrf

        <div class="field-row-2col">
          <div class="control-group">
            <label class="field-label" for="reg-nom">Nom</label>
            <div class="input-icon-wrapper">
              <i class="bi bi-person input-icon"></i>
              <input type="text" name="nom" placeholder="Koné" id="reg-nom" required>
            </div>
          </div>
          <div class="control-group">
            <label class="field-label" for="reg-prenom">Prénom</label>
            <div class="input-icon-wrapper">
              <i class="bi bi-person input-icon"></i>
              <input type="text" name="prenom" placeholder="Aya" id="reg-prenom" required>
            </div>
          </div>
        </div>

        <div class="field-row-2col">
          <div class="control-group">
            <label class="field-label" for="reg-age">Âge</label>
            <div class="input-icon-wrapper">
              <i class="bi bi-calendar3 input-icon"></i>
              <input type="number" name="age" placeholder="25" id="reg-age" required>
            </div>
          </div>
          <div class="control-group">
            <label class="field-label" for="reg-profession">Profession</label>
            <div class="input-icon-wrapper">
              <i class="bi bi-briefcase input-icon"></i>
              <input type="text" name="profession" placeholder="Ingénieur" id="reg-profession" required>
            </div>
          </div>
        </div>

        <div class="control-group">
          <label class="field-label" for="reg-phone">Numéro de téléphone</label>
          <div class="phone-input-wrapper">
            <div class="phone-prefix">
              <span class="flag">🇨🇮</span>
              <span>+225</span>
            </div>
            <input type="tel" name="phone" class="phone-field" placeholder="0X XX XX XX XX"
              id="reg-phone" required pattern="^(?:01|05|07)[0-9]{8}$"
              inputmode="numeric" maxlength="10"
              title="Numéro ivoirien à 10 chiffres, commence par 01, 05 ou 07">
          </div>
          <p class="field-hint">
            <i class="bi bi-info-circle"></i>
            Commence par 01, 05 ou 07 — 10 chiffres
          </p>
        </div>

        <div class="alert alert-info text-start d-none" id="register-status" role="alert"></div>

        <button type="submit" class="btn" id="register-btn">
          <i class="bi bi-person-check"></i>
          S'enregistrer
        </button>
      </form>

      <div class="login-footer">
        <span style="font-size: 0.8125rem; color: #888;">Déjà un compte ?</span>
        <a href="{{ url('/login-control') }}" class="login-link" style="margin-left: 6px;">Se connecter</a>
      </div>
    </div>

    <p class="login-copyright">© 2025 EPA — Accès sécurisé</p>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('register-form');
    const registerBtn = document.getElementById('register-btn');
    const statusBox = document.getElementById('register-status');
    const phoneInput = document.getElementById('reg-phone');

    form.addEventListener('submit', function () {
      registerBtn.disabled = true;
      registerBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Traitement...';
    });

    phoneInput.addEventListener('input', function () {
      this.value = this.value.replace(/\D/g, '').slice(0, 10);
      statusBox.classList.add('d-none');
    });
  });
</script>
@endsection