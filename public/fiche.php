@extends('index')

@section('contain')
    <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
    <a href="#" class="btn-back-floating" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="bi bi-box-arrow-left"></i>
    </a>
    <div class="container">
        <div class="welcome-logo">
            <img src="{{ asset('images/logo epa.jpg.jpeg') }}" alt="Logo EPA">
        </div>

        <div class="game-container">
            <div class="roulette-wrapper">
                <div class="pointer"></div>
                <canvas id="wheel" width="400" height="400" class="roulette-wheel"></canvas>
            </div>

            <div class="game-controls mt-4">
                <button id="spin-btn" class="btn btn-large">Tenter ma chance</button>
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
                    <p id="modal-message" class="fs-4"></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const wheel = document.getElementById('wheel');
            const spinBtn = document.getElementById('spin-btn');

            // Elements du modal
            const resultModal = new bootstrap.Modal(document.getElementById('resultModal'));
            const modalTitle = document.getElementById('modal-title');
            const modalMessage = document.getElementById('modal-message');

            // Récupération des cadeaux depuis la base de données via Blade
            let segments = @json($gifts);

            // Si aucun cadeau en base, on met des valeurs par défaut pour tester
            if (segments.length === 0) {
                segments = [
                    { name: "Cadeau 1", image: null },
                    { name: "Perdu", image: null },
                    { name: "Cadeau 2", image: null },
                    { name: "Rejouez", image: null }
                ];
            } else {
                // Ajout de la section "Perdu" à la liste des cadeaux
                segments.push({ name: "Perdu", image: null });
            }

            // Configuration du Canvas
            const ctx = wheel.getContext('2d');
            const centerX = wheel.width / 2;
            const centerY = wheel.height / 2;
            const radius = wheel.width / 2;
            const colors = ['#ffffff', '#680202',]; // Rouge EPA et Blanc

            let currentRotation = 0;

            // Fonction pour dessiner la roue
            function drawWheel() {
                // Nettoyer le canvas avant de redessiner pour éviter les superpositions
                ctx.clearRect(0, 0, wheel.width, wheel.height);

                const arc = (2 * Math.PI) / segments.length;

                segments.forEach((segment, i) => {
                    const angle = i * arc - Math.PI / 2; // -PI/2 pour commencer à midi (haut)

                    ctx.beginPath();
                    ctx.fillStyle = colors[i % colors.length];
                    ctx.moveTo(centerX, centerY);
                    ctx.arc(centerX, centerY, radius, angle, angle + arc);
                    ctx.lineTo(centerX, centerY);
                    ctx.fill();

                    // Dessin du texte et de l'image
                    ctx.save();
                    ctx.translate(centerX, centerY);
                    ctx.rotate(angle + arc / 2);
                    ctx.textAlign = "right";
                    ctx.fillStyle = (i % colors.length === 0) ? "#000000" : "#fff";
                    ctx.font = "bold 14px Arial";

                    // Si une image est chargée, on l'affiche
                    if (segment.imgObj) {
                        // On dessine l'image et on ajuste la position du texte pour éviter le chevauchement
                        ctx.drawImage(segment.imgObj, radius - 70, -20, 40, 40);
                        ctx.fillText(segment.name, radius - 80, 5);
                    } else {
                        // S'il n'y a pas d'image, on affiche juste le texte
                        ctx.fillText(segment.name, radius - 20, 5);
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
                        img.src = `{{ asset('images') }}/${seg.image}`;
                        img.onload = () => { seg.imgObj = img; resolve(); };
                        img.onerror = () => { console.error(`Impossible de charger l'image : ${seg.image}`); resolve(); };
                    });
                });

                // Une fois que toutes les images sont chargées (ou en erreur), on dessine la roue
                Promise.all(imagePromises).then(() => { drawWheel(); });
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

                    const prizeName = segments[index].name;

                    if (prizeName === "Perdu" || prizeName === "Rejouez") {
                        modalTitle.innerText = "Dommage !";
                        modalMessage.innerText = "Vous avez perdu. Retentez votre chance !";
                    } else {
                        modalTitle.innerText = "Félicitations !";
                        modalMessage.innerText = "Vous avez gagné : " + prizeName;

                        // Lancement des confettis
                        confetti({
                            particleCount: 150,
                            spread: 70,
                            origin: { y: 0.6 }
                        });
                    }

                    resultModal.show();

                    spinBtn.innerText = "Rejouer";
                    spinBtn.disabled = false;
                }, 5000); // Correspond à la durée de la transition CSS
            });
        });
    </script>
@endsection
