@extends('index')
@section('contain')

<div class="welcome-page-wrapper">

  <div class="welcome-intro">
    <p class="welcome-eyebrow">Elles Plus Africa</p>
    <h1>Bienvenue !</h1>
    <p class="welcome-sub">Participez au <strong>Grand Jeu</strong> et tentez de remporter de superbes cadeaux.</p>
  </div>

  <div class="welcome-visual">
    <div class="welcome-orbit"></div>
    <div class="welcome-wheel-bg">
      <img src="{{ asset('images/roulette.png') }}" alt="Roulette" class="welcome-wheel-img">
    </div>
    <i class="bi bi-gift-fill welcome-float wf-1"></i>
    <i class="bi bi-stars welcome-float wf-2"></i>
    <i class="bi bi-trophy-fill welcome-float wf-3"></i>
    <i class="bi bi-bag-heart-fill welcome-float wf-4"></i>
  </div>

  <div class="welcome-actions">
    <a href="{{ url('/register') }}" class="welcome-btn-primary">
      🎉 Commencer
    </a>
    <a href="{{ url('/login') }}" class="welcome-btn-ghost">
      Déjà inscrit ? Se connecter
    </a>
  </div>

  <div class="welcome-stats">
    <div class="welcome-stat">
      <span class="welcome-stat-num">100%</span>
      <span class="welcome-stat-label">Gratuit</span>
    </div>
    <div class="welcome-stat-sep"></div>
    <div class="welcome-stat">
      <span class="welcome-stat-num">+5</span>
      <span class="welcome-stat-label">Lots à gagner</span>
    </div>
    <div class="welcome-stat-sep"></div>
    <div class="welcome-stat">
      <span class="welcome-stat-num">1</span>
      <span class="welcome-stat-label">Participation</span>
    </div>
  </div>

  <p class="login-copyright">© 2025 EPA — Accès sécurisé</p>
</div>

@endsection