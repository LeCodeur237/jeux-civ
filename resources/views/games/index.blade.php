@extends('index')
@section('contain')

<div class="games-page-wrapper">

  <div class="login-hero">
    <div class="login-hero-avatar">
      <i class="bi bi-controller" style="font-size: 1.8rem; color: #fff;"></i>
    </div>
    <h1>Nos jeux</h1>
    <p>Choisissez votre jeu et tentez de gagner des cadeaux</p>
  </div>

  <div class="games-grid">

    <div class="game-card-new">
      <div class="game-card-visual game-card-visual--blue">
        <div class="game-card-icon-wrap">
          <i class="bi bi-arrow-repeat" style="font-size: 3.5rem; color: #00347d;"></i>
        </div>
        <span class="game-badge game-badge--blue">Disponible</span>
      </div>
      <div class="game-card-body">
        <h2>Roulette</h2>
        <p>Faites tourner la roue et découvrez si la chance est avec vous. Des cadeaux à gagner à chaque tour !</p>
        <div class="game-card-actions">
          <a href="{{ url('/roulette') }}" class="game-btn game-btn--primary">
            <i class="bi bi-play-fill"></i> Jouer
          </a>
          <a href="{{ url('/register') }}" class="game-btn game-btn--outline-blue">
            <i class="bi bi-person-plus"></i> S'inscrire
          </a>
        </div>
      </div>
    </div>

    <div class="game-card-new">
      <div class="game-card-visual game-card-visual--red">
        <div class="game-card-icon-wrap">
          <i class="bi bi-grid-3x2-gap" style="font-size: 3.5rem; color: #7d1900;"></i>
        </div>
        <span class="game-badge game-badge--red">Saint-Valentin</span>
      </div>
      <div class="game-card-body">
        <h2>Jeu à gratter</h2>
        <p>Grattez et révélez votre lot caché. Surprise garantie — avez-vous la main chanceuse ?</p>
        <div class="game-card-actions">
          <a href="{{ url('/valentines-day') }}" class="game-btn game-btn--red">
            <i class="bi bi-play-fill"></i> Jouer
          </a>
        </div>
      </div>
    </div>

  </div>

  <p class="login-copyright">© 2025 EPA — Accès sécurisé</p>
</div>

@endsection