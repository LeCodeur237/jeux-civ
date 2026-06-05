@extends('index')
@section('contain')
<div class="login-page-wrapper">
  <div class="login">

    <div class="login-hero">
      <div class="login-hero-avatar">
        <img src="{{ asset('images/logo epa.jpg.jpeg') }}" alt="Logo EPA">
      </div>
      <h1>EPA</h1>
      <p>Connectez-vous à votre espace</p>
    </div>

    <div class="login-screen">
      <form action="{{ url('/login-control') }}" method="POST" class="login-form" id="login-form">
        @csrf

        <div class="control-group">
          <label class="field-label" for="login-phone">Numéro de téléphone</label>
          <div class="phone-input-wrapper">
            <div class="phone-prefix">
              <span class="flag">🇨🇮</span>
              <span>+225</span>
            </div>
            <input type="tel" name="phone" class="phone-field" placeholder="0X XX XX XX XX"
              id="login-phone" required pattern="^(?:01|05|07)[0-9]{8}$"
              inputmode="numeric" maxlength="10"
              title="Numéro ivoirien à 10 chiffres, commence par 01, 05 ou 07">
          </div>
        </div>

        <div class="control-group">
          <label class="field-label" for="login-pass">Mot de passe</label>
          <div class="password-wrapper">
            <input type="password" name="password" placeholder="••••••••" id="login-pass" required>
            <button type="button" class="toggle-password" id="togglePassword" aria-label="Afficher le mot de passe">
              <i class="bi bi-eye"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="btn" id="login-btn">
          Se connecter
        </button>
      </form>

      <div class="login-footer">
        <a href="#" class="login-link">Mot de passe oublié ?</a>
      </div>
    </div>

    <p class="login-copyright">© 2025 EPA — Accès sécurisé</p>
  </div>
</div>

<script>
  document.getElementById('login-form').addEventListener('submit', function() {
    const btn = document.getElementById('login-btn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border" role="status" aria-hidden="true"></span> Connexion...';
  });

  const togglePassword = document.querySelector('#togglePassword');
  const password = document.querySelector('#login-pass');
  togglePassword.addEventListener('click', function() {
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);
    this.querySelector('i').classList.toggle('bi-eye');
    this.querySelector('i').classList.toggle('bi-eye-slash');
  });
</script>
@endsection