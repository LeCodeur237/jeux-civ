@extends('index')

@section('contain')
    <style>
        .result-art {
            width: 180px;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .win-badge {
            animation: winPulse 1.4s ease-in-out infinite;
        }

        .lose-badge {
            animation: loseShake 0.8s ease-in-out infinite;
            filter: saturate(0.9);
        }

        @keyframes winPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.06); }
        }

        @keyframes loseShake {
            0%, 100% { transform: translateX(0); }
            20% { transform: translateX(-4px) rotate(-1deg); }
            40% { transform: translateX(4px) rotate(1deg); }
            60% { transform: translateX(-3px) rotate(-1deg); }
            80% { transform: translateX(3px) rotate(1deg); }
        }
    </style>

    <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <div class="container">
        <div class="welcome-logo mb-5">
            <img src="{{ asset('images/logo epa.jpg.jpeg') }}" alt="Logo EPA">
        </div>

        <div class="login">
            <div class="login-screen text-center">
                @if($hasPlayed)
                    @if($isWinner)
                        <div class="app-title mb-4">
                            <img src="{{ asset('images/succes.gif') }}" alt="Succès" class="result-art win-badge" id="result-art">
                            <h1 class="mt-2">Félicitations !</h1>
                        </div>
                        <p class="mb-4">Vous avez gagné : <strong>{{ $prize }}</strong></p>
                    @else
                        <div class="app-title mb-4">
                            <img src="{{ asset('images/echec.gif') }}" alt="Échec" class="result-art lose-badge" id="result-art">
                            <h1 class="mt-2">Dommage !</h1>
                        </div>
                        <p class="mb-4">Vous avez perdu. Merci pour votre participation.</p>
                    @endif

                    <div class="d-grid gap-2">
                        <a href="#" class="btn btn-large"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Quitter le jeu
                        </a>
                    </div>
                @else
                    <div class="app-title mb-4">
                        <h1>Résultat indisponible</h1>
                    </div>
                    <p class="mb-4">Aucun résultat n'a encore été enregistré pour votre compte.</p>
                    <a href="{{ route('home') }}" class="btn btn-large">Retour au jeu</a>
                @endif
            </div>
        </div>
    </div>

    @if($isWinner)
        <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                function fireConfetti() {
                    confetti({
                        particleCount: 150,
                        spread: 70,
                        origin: { y: 0.6 },
                        zIndex: 2000
                    });
                }

                fireConfetti();
                setInterval(fireConfetti, 20000);
            });
        </script>
    @endif
@endsection
