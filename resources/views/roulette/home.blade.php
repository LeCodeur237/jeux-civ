@extends('index')

@section('contain')
    <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
    <a href="#" class="btn-back-floating"
        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="bi bi-box-arrow-left"></i>
    </a>
    <div class="container">
        <div class="welcome-logo mb-5">
            <img src="{{ asset('images/logo epa.jpg.jpeg') }}" alt="Logo EPA">
        </div>

        <div class="game-container">
            <div class="roulette-wrapper">
                <div class="pointer"></div>
                <canvas id="wheel" width="300" height="300" class="roulette-wheel"></canvas>
            </div>

            <div class="game-controls mt-4">
                <button id="spin-btn" class="btn btn-large">Tenter ma chance</button>
            </div>
        </div>
        <div class="partners-logo mt-4">
            <h6 class="text-center">Nos partenaires : </h6>
            <div class="d-flex justify-content-center align-items-center gap-3 flex-wrap">
                <img src="{{ asset('images/partner-1.jpeg') }}" alt="GIMUEMOA" style="height: 40px; object-fit: contain;">
                <img src="{{ asset('images/partner-2.jpeg') }}" alt="SUNU Assurances"
                    style="height: 40px; object-fit: contain;">
                <img src="{{ asset('images/partner-3.jpeg') }}" alt="GES-CI" style="height: 40px; object-fit: contain;">
            </div>
        </div>
    </div>

    <!-- Modal Résultat -->
    <div class="modal fade" id="resultModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center pb-5">
                    <h2 id="modal-title" class="mb-3" style="color: #7d1900; font-weight: bold;"></h2>
                    <div id="modal-message" class="fs-4"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function fireConfetti() {
                confetti({
                    particleCount: 150,
                    spread: 70,
                    origin: {
                        y: 0.6
                    },
                    zIndex: 2000
                });
            }

            let confettiIntervalId = null;
            function startConfettiEvery10s() {
                if (confettiIntervalId !== null) return;
                fireConfetti(); // premier tir immediat
                confettiIntervalId = setInterval(fireConfetti, 5000);
            }

            const wheel = document.getElementById('wheel');
            const spinBtn = document.getElementById('spin-btn');

            @if (Auth::user()->played_games)
                spinBtn.disabled = true;
                spinBtn.innerText = "Vous avez déjà joué";
            @endif

            // Elements du modal
            const resultModal = new bootstrap.Modal(document.getElementById('resultModal'));
            const modalTitle = document.getElementById('modal-title');
            const modalMessage = document.getElementById('modal-message');

            // Récupération des cadeaux depuis la base de données via Blade
            let segments = @json($gifts);

            // Si aucun cadeau en base, on met des valeurs par défaut pour tester
            if (segments.length === 0) {
                segments = [{
                        name: "T-shirt",
                        image: null
                    },
                    {
                        name: "Casquette",
                        image: null
                    },
                    {
                        name: "Panier",
                        image: null
                    },
                    {
                        name: "Bloc note",
                        image: null
                    },
                    {
                        name: "Bol",
                        image: null
                    },
                    {
                        name: "Perdu",
                        image: null
                    }
                ];
            } else {
                // Ajout de la section "Perdu" à la liste des cadeaux
                segments.push({
                    name: "Perdu",
                    image: null
                });
            }

            // Configuration du Canvas
            const ctx = wheel.getContext('2d');
            const centerX = wheel.width / 2;
            const centerY = wheel.height / 2;
            const radius = wheel.width / 2;
            const palette = ['#FFC107', '#4CAF50', '#2196F3', '#9C27B0', '#FF5722', '#795548', '#607D8B'];

            let currentRotation = 0;

            // Fonction pour dessiner la roue
            function drawWheel() {
                // Nettoyer le canvas avant de redessiner pour éviter les superpositions
                ctx.clearRect(0, 0, wheel.width, wheel.height);

                const arc = (2 * Math.PI) / segments.length;

                segments.forEach((segment, i) => {
                    const angle = i * arc - Math.PI / 2; // -PI/2 pour commencer à midi (haut)

                    ctx.beginPath();
                    if (segment.name === 'Perdu') {
                        ctx.fillStyle = '#680202'; // Rouge uniquement pour Perdu
                    } else {
                        ctx.fillStyle = palette[i % palette.length]; // Couleurs variées pour les autres
                    }
                    ctx.moveTo(centerX, centerY);
                    ctx.arc(centerX, centerY, radius, angle, angle + arc);
                    ctx.lineTo(centerX, centerY);
                    ctx.fill();
                    ctx.lineWidth = 3;
                    ctx.strokeStyle = '#fff';
                    ctx.stroke();

                    // Dessin du texte et de l'image
                    ctx.save();
                    ctx.translate(centerX, centerY);
                    ctx.rotate(angle + arc / 2);
                    ctx.textAlign = "right";

                    // Si une image est chargée, on l'affiche
                    if (segment.imgObj) {
                        // On dessine l'image uniquement (sans texte)
                        ctx.drawImage(segment.imgObj, radius - 80, -30, 60, 60);
                    } else if (segment.name === 'Perdu') {
                        // Afficher "Perdu" en blanc
                        ctx.fillStyle = "#ffffff";
                        ctx.font = "bold 20px Arial";
                        ctx.fillText(segment.name, radius - 30, 5);
                    }

                    ctx.restore();
                });
            }

            // Fonction pour charger les images et lancer le dessin de manière fiable
            function loadAssetsAndDraw() {
                const imagePromises = segments.map(seg => {
                    if (!seg.image) return Promise.resolve(); // Pas d'image, on résout immédiatement
                    return new Promise(resolve => {
                        const img = new Image();
                        img.src = seg.image;
                        img.onload = () => {
                            seg.imgObj = img;
                            resolve();
                        };
                        img.onerror = () => {
                            console.error(`Impossible de charger l'image : ${seg.image}`);
                            resolve();
                        };
                    });
                });

                // Une fois que toutes les images sont chargées (ou en erreur), on dessine la roue
                Promise.all(imagePromises).then(() => {
                    drawWheel();
                });
            }

            loadAssetsAndDraw();

            spinBtn.addEventListener('click', function() {
                spinBtn.disabled = true;

                // Calcul d'une rotation aléatoire : au moins 5 tours (1800 deg) + aléatoire
                const randomSpins = Math.floor(Math.random() * 5) + 5;
                const randomAngle = Math.floor(Math.random() * 360);
                const totalRotation = (randomSpins * 360) + randomAngle;

                // On ajoute à la rotation actuelle pour une animation fluide
                currentRotation += totalRotation;

                wheel.style.transition = 'transform 5s cubic-bezier(0.25, 0.1, 0.25, 1)';
                wheel.style.transform = `rotate(${currentRotation}deg)`;

                setTimeout(() => {
                    // Calcul du segment gagnant
                    const actualDeg = currentRotation % 360;
                    const segmentAngle = 360 / segments.length;
                    // Ajustement car on a dessiné en commençant à -90deg (haut)
                    const winningAngle = (360 - actualDeg) % 360;
                    const index = Math.floor(winningAngle / segmentAngle);

                    const winningSegment = segments[index];
                    const prizeName = winningSegment.name;

                    // Enregistrement du résultat en base de données
                    fetch('{{ url('/save-game-result') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            prize: prizeName
                        })
                    });

                    if (prizeName === "Perdu" || prizeName === "Rejouez") {
                        modalTitle.innerText = "Dommage !";
                        modalMessage.innerHTML = `<img src="{{ asset('images/echec.gif') }}" alt="Echec" class="img-fluid mb-3" style="max-height: 150px;"><p>Vous avez perdu. Retentez votre chance !</p>`;
                    } else {
                        modalTitle.innerText = "Félicitations !";

                        let messageContent = `<img src="{{ asset('images/succes.gif') }}" alt="Succès" class="img-fluid mb-3" style="max-height: 150px;">`;
                        messageContent += `<p class="mb-3">Vous avez gagné : <b>${prizeName}</b></p>`;
                        if (winningSegment.image) {
                            messageContent +=
                                `<img src="${winningSegment.image}" alt="${prizeName}" class="img-fluid rounded" style="max-height: 120px; border : 2px solid #7d1900; padding: 5px;">`;
                        }
                        modalMessage.innerHTML = messageContent;

                        // Lancement des confettis
                        startConfettiEvery10s();
                    }

                    resultModal.show();

                    spinBtn.innerText = "Terminé";
                }, 5000); // Correspond à la durée de la transition CSS
            });
        });
    </script>
@endsection
