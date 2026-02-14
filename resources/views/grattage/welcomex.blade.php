@extends('index')

@section('contain')
    <div class="back grattage-welcome"
        style="background-image: linear-gradient(to top, rgba(19, 0, 0, 0.928), rgba(25, 0, 0, 0.308)), url('{{ asset('images/51941.jpg') }}'); background-size: cover; background-position: bottom; background-repeat: no-repeat;">
        <div class="container grattage-welcome-container">
            <div class="welcome-content">
                <h1>Jeu instant gagnant</h1>
                <p>Special St Valentin</p>
            </div>



            <div class="d-grid gap-1 w-100">
                <div class="lots-preview">
                    <div class="lots-title">Lots à gagner</div>
                    <a href="#" class="lots-link" data-bs-toggle="modal" data-bs-target="#lotsModal">
                        Voir tous les cadeaux
                    </a>
                </div>
                <span class="text-center text-white">+100 personnes ont déjà joué.</span>
                {{-- <a href="{{ url('/registering') }}" class="btn btn-large"
                    style="text-align: center; font-size: 2rem;"><b>Jouer</b></a> --}}
                <button type="button" class="btn btn-large" data-bs-toggle="modal" data-bs-target="#gameEndedModal"
                    style="text-align: center; font-size: 2rem;"><b>Jouer</b></button>
            </div>
        </div>
    </div>

    <div class="modal fade valentine-modal" id="lotsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Lots à gagner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="lots-image" aria-label="Image des lots a gagner">
                        <div class="lot-item">
                            <img src="{{ asset('img/lots-1.jpeg') }}" alt="Un dîner pour deux au restaurant LE LOF">
                            <p class="lot-caption">Un dîner pour deux au restaurant LE LOF</p>
                        </div>
                        <div class="lot-item">
                            <img src="{{ asset('img/lots-2.jpeg') }}" alt="Un contrat d’assurance MonAPPUI pour deux">
                            <p class="lot-caption">Un contrat d’assurance MonAPPUI pour deux</p>
                        </div>
                        <div class="lot-item">
                            <img src="{{ asset('img/lots-3.jpeg') }}"
                                alt="Une prestation d’extension de cils de chez ELIAB">
                            <p class="lot-caption">Une prestation d’extension de cils de chez ELIAB</p>
                        </div>
                        <div class="lot-item">
                            <img src="{{ asset('img/lots-4.jpeg') }}" alt="Un bouquet de fleurs">
                            <p class="lot-caption">Un somptueux bouquet de roses nature</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade valentine-modal" id="gameEndedModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Jeu terminé</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p class="mb-0">Le jeu est terminé. Merci pour votre participation.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
