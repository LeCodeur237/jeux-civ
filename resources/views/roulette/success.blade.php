@extends('index')
@section('contain')

<div class="success-page-wrapper">

  <div class="success-hero">
    <div class="success-avatar">
      <i class="bi bi-check-circle" style="font-size: 2.5rem; color: #fff;"></i>
    </div>
    <h1>Inscription réussie !</h1>
    <p>Conservez précieusement vos identifiants pour vous connecter au jeu.</p>
  </div>

  <div class="login-screen success-card">

    <div class="success-alert">
      <div class="success-alert-icon">
        <i class="bi bi-info-circle-fill" style="font-size: 1.1rem; color: #185FA5;"></i>
      </div>
      <p>Notez ces informations avant de continuer — elles ne seront plus affichées.</p>
    </div>

    <div class="control-group">
      <label class="field-label">Numéro (identifiant)</label>
      <div class="cred-field">
        <i class="bi bi-telephone cred-icon"></i>
        <span class="cred-value">{{ session('phone') }}</span>
      </div>
    </div>

    <div class="control-group">
      <label class="field-label">Mot de passe</label>
      <div class="cred-field cred-field--copyable" id="copy-btn" title="Copier le mot de passe" role="button" tabindex="0">
        <i class="bi bi-key cred-icon"></i>
        <span class="cred-value" id="password-text">{{ session('password') }}</span>
        <div class="copy-action" id="copy-feedback">
          <i class="bi bi-clipboard" id="copy-icon"></i>
          <span id="copy-label">Copier</span>
        </div>
      </div>
    </div>

    <a href="{{ url('/login-form') }}" class="btn mt-2">
      <i class="bi bi-play-fill"></i>
      Jouer maintenant
    </a>
  </div>

  <p class="login-copyright">© 2025 EPA — Accès sécurisé</p>
</div>

<script>
  document.getElementById('copy-btn').addEventListener('click', function () {
    const text = document.getElementById('password-text').innerText;
    navigator.clipboard.writeText(text).then(() => {
      const icon  = document.getElementById('copy-icon');
      const label = document.getElementById('copy-label');
      const btn   = document.getElementById('copy-btn');
      icon.className  = 'bi bi-check-lg';
      label.textContent = 'Copié !';
      btn.classList.add('cred-field--copied');
      setTimeout(() => {
        icon.className  = 'bi bi-clipboard';
        label.textContent = 'Copier';
        btn.classList.remove('cred-field--copied');
      }, 2500);
    });
  });
</script>

@endsection