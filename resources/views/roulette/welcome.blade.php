@extends('index')

@section('contain')
    <div class="container">
        <div class="welcome-logo">
            <img src="{{ asset('images/logo epa.jpg.jpeg') }}" alt="Logo EPA">
        </div>
        <div class="welcome-screen">
            <div class="roulette-container">
                <img src="{{ asset('images/roulette.png') }}" alt="Roulette" class="roulette-wheel">
                <i class="bi bi-gift-fill floating-icon icon-1"></i>
                <i class="bi bi-stars floating-icon icon-2"></i>
                <i class="bi bi-trophy-fill floating-icon icon-3"></i>
                <i class="bi bi-bag-heart-fill floating-icon icon-4"></i>
            </div>
            <div class="welcome-content">
                <h1>Bienvenue !</h1>
                <p>Participez au Grand Jeu Elles Plus Africa ! Le principe est simple : c'est une roulette.
                </p>
            </div>
            <a href="{{ url('/register') }}" class="btn">Commencer</a>
            <div class="partners-logo mt-4">
                <h6>Nos partenaires : </h6>
                <div class="d-flex justify-content-center align-items-center gap-3 flex-wrap">
                    <img src="{{ asset('images/partner-1.jpeg') }}" alt="GIMUEMOA" style="height: 40px; object-fit: contain;">
                    <img src="{{ asset('images/partner-2.jpeg') }}" alt="SUNU Assurances" style="height: 40px; object-fit: contain;">
                    <img src="{{ asset('images/partner-3.jpeg') }}" alt="GES-CI" style="height: 40px; object-fit: contain;">
                </div>
            </div>

        </div>
    </div>
@endsection
