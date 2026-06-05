@extends('index')
@section('contain')

<form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display:none;">
  @csrf
</form>

<div class="result-page-wrapper {{ $hasPlayed && $isWinner ? 'result-page--win' : '' }}">

  @if($hasPlayed)

    @if($isWinner)

      <div class="result-hero" style="animation: fade-up 0.6s ease both;">
        <div class="result-avatar result-avatar--win">
          <img src="{{ asset('images/succes.gif') }}" alt="Succès" class="result-gif">
        </div>
        <h1 class="result-title">Félicitations !</h1>
        <p class="result-sub">Vous avez participé au Grand Jeu EPA</p>
      </div>

      <div class="login-screen result-card">
        <div class="result-prize-box">
          <p class="result-prize-label">Votre lot</p>
          <p class="result-prize-name">{{ $prize }}</p>
        </div>
        <div class="result-info result-info--green">
          <i class="bi bi-info-circle-fill"></i>
          <p>Un conseiller EPA vous contactera pour la remise de votre cadeau.</p>
        </div>
        <button class="btn" onclick="document.getElementById('logout-form').submit()">
          <i class="bi bi-box-arrow-right"></i>
          Quitter le jeu
        </button>
      </div>

    @else

      <div class="result-hero" style="animation: fade-up 0.6s ease both;">
        <div class="result-avatar result-avatar--lose">
          <img src="{{ asset('images/echec.gif') }}" alt="Échec" class="result-gif result-gif--lose">
        </div>
        <h1 class="result-title">Dommage !</h1>
        <p class="result-sub">Vous avez perdu cette fois-ci. Merci pour votre participation !</p>
      </div>

      <div class="login-screen result-card">
        <div class="result-prize-box result-prize-box--lose">
          <p class="result-prize-label">Résultat</p>
          <p class="result-prize-name result-prize-name--lose">Pas de lot cette fois</p>
        </div>
        <div class="result-info result-info--blue">
          <i class="bi bi-heart-fill"></i>
          <p>Merci d'avoir participé au Grand Jeu EPA. Restez à l'écoute pour les prochaines éditions !</p>
        </div>
        <button class="btn" onclick="document.getElementById('logout-form').submit()">
          <i class="bi bi-box-arrow-right"></i>
          Quitter le jeu
        </button>
      </div>

    @endif

  @else

    <div class="result-hero" style="animation: fade-up 0.6s ease both;">
      <div class="result-avatar result-avatar--neutral">
        <i class="bi bi-question-lg" style="font-size: 2.5rem; color: #fff;"></i>
      </div>
      <h1 class="result-title">Résultat indisponible</h1>
      <p class="result-sub">Aucun résultat n'a encore été enregistré pour votre compte.</p>
    </div>

    <div class="login-screen result-card">
      <div class="result-info result-info--blue" style="margin-bottom: 1.5rem;">
        <i class="bi bi-info-circle-fill"></i>
        <p>Participez au jeu pour voir votre résultat apparaître ici.</p>
      </div>
      <a href="{{ route('home') }}" class="btn">
        <i class="bi bi-arrow-left"></i>
        Retour au jeu
      </a>
    </div>

  @endif

  <p class="login-copyright">© 2025 EPA — Accès sécurisé</p>
</div>

@if($hasPlayed && $isWinner)
  <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      function fireConfetti() {
        confetti({ particleCount: 150, spread: 70, origin: { y: 0.6 }, zIndex: 2000 });
      }
      fireConfetti();
      setInterval(fireConfetti, 20000);
    });
  </script>
@endif

@endsection