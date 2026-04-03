@extends('index')

@section('contain')
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
                fireConfetti();
                confettiIntervalId = setInterval(fireConfetti, 5000);
            }

            const wheel = document.getElementById('wheel');
            const spinBtn = document.getElementById('spin-btn');
            const resultModal = new bootstrap.Modal(document.getElementById('resultModal'));
            const modalTitle = document.getElementById('modal-title');
            const modalMessage = document.getElementById('modal-message');
            const assetBase = "{{ asset('images') }}";

            @if (Auth::user()->played_games)
                spinBtn.disabled = true;
                spinBtn.innerText = "Vous avez déjà joué";
            @endif

            let segments = @json($gifts);

            if (segments.length === 0) {
                segments = [
                    { name: "T-shirt", image: null },
                    { name: "Casquette", image: null },
                    { name: "Panier", image: null },
                    { name: "Bloc note", image: null },
                    { name: "Bol", image: null },
                    { name: "Perdu", image: null }
                ];
            } else {
                segments.push({ name: "Perdu", image: null });
            }

            const ctx = wheel.getContext('2d');
            const centerX = wheel.width / 2;
            const centerY = wheel.height / 2;
            const radius = wheel.width / 2;
            const palette = ['#FFC107', '#4CAF50', '#2196F3', '#9C27B0', '#FF5722', '#795548', '#607D8B'];
            let currentRotation = 0;

            function resolveImageUrl(path) {
                if (!path) return null;
                if (/^(https?:)?\/\//.test(path) || path.startsWith('/')) return path;
                return `${assetBase}/${path}`;
            }

            function drawWheel() {
                ctx.clearRect(0, 0, wheel.width, wheel.height);

                const arc = (2 * Math.PI) / segments.length;

                segments.forEach((segment, i) => {
                    const angle = i * arc - Math.PI / 2;

                    ctx.beginPath();
                    ctx.fillStyle = segment.name === 'Perdu' ? '#680202' : palette[i % palette.length];
                    ctx.moveTo(centerX, centerY);
                    ctx.arc(centerX, centerY, radius, angle, angle + arc);
                    ctx.lineTo(centerX, centerY);
                    ctx.fill();
                    ctx.lineWidth = 3;
                    ctx.strokeStyle = '#fff';
                    ctx.stroke();

                    ctx.save();
                    ctx.translate(centerX, centerY);
                    ctx.rotate(angle + arc / 2);
                    ctx.textAlign = "right";

                    if (segment.imgObj) {
                        ctx.drawImage(segment.imgObj, radius - 80, -30, 60, 60);
                    } else if (segment.name === 'Perdu') {
                        ctx.fillStyle = "#ffffff";
                        ctx.font = "bold 20px Arial";
                        ctx.fillText(segment.name, radius - 30, 5);
                    }

                    ctx.restore();
                });
            }

            function loadAssetsAndDraw() {
                const imagePromises = segments.map(seg => {
                    const imageUrl = resolveImageUrl(seg.image);
                    if (!imageUrl) return Promise.resolve();

                    return new Promise(resolve => {
                        const img = new Image();
                        img.src = imageUrl;
                        img.onload = () => {
                            seg.imgObj = img;
                            resolve();
                        };
                        img.onerror = () => {
                            console.error(`Impossible de charger l'image : ${imageUrl}`);
                            resolve();
                        };
                    });
                });

                Promise.all(imagePromises).then(() => {
                    drawWheel();
                });
            }

            function rotationForIndex(index) {
                const segmentAngle = 360 / segments.length;
                const segmentCenter = (index * segmentAngle) + (segmentAngle / 2);
                return (360 - segmentCenter) % 360;
            }

            function showResult(prizeName, prizeImage) {
                if (prizeName === "Perdu" || prizeName === "Rejouez") {
                    modalTitle.innerText = "Dommage !";
                    modalMessage.innerHTML = `<img src="{{ asset('images/echec.gif') }}" alt="Echec" class="img-fluid mb-3" style="max-height: 150px;"><p>Vous avez perdu. Retentez votre chance !</p>`;
                    resultModal.show();
                    spinBtn.innerText = "Terminé";
                    return;
                }

                modalTitle.innerText = "Félicitations !";

                let messageContent = `<img src="{{ asset('images/succes.gif') }}" alt="Succès" class="img-fluid mb-3" style="max-height: 150px;">`;
                messageContent += `<p class="mb-3">Vous avez gagné : <b>${prizeName}</b></p>`;
                if (prizeImage) {
                    messageContent += `<img src="${prizeImage}" alt="${prizeName}" class="img-fluid rounded" style="max-height: 120px; border : 2px solid #7d1900; padding: 5px;">`;
                }
                modalMessage.innerHTML = messageContent;

                startConfettiEvery10s();
                resultModal.show();
                spinBtn.innerText = "Terminé";
            }

            loadAssetsAndDraw();

            spinBtn.addEventListener('click', async function() {
                spinBtn.disabled = true;

                try {
                    const response = await fetch('{{ url('/save-game-result') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    const data = await response.json();

                    if (!response.ok || !data.success) {
                        throw new Error(data.message || 'Impossible de traiter le tirage.');
                    }

                    const prizeName = data.prize || 'Perdu';
                    const prizeIndex = segments.findIndex(segment => segment.name === prizeName);
                    const landingIndex = prizeIndex >= 0 ? prizeIndex : segments.findIndex(segment => segment.name === 'Perdu');
                    const randomSpins = Math.floor(Math.random() * 5) + 5;
                    const targetRotation = rotationForIndex(landingIndex >= 0 ? landingIndex : 0);

                    currentRotation += (randomSpins * 360) + targetRotation;
                    wheel.style.transition = 'transform 5s cubic-bezier(0.25, 0.1, 0.25, 1)';
                    wheel.style.transform = `rotate(${currentRotation}deg)`;

                    setTimeout(() => {
                        window.location.href = data.redirect || "{{ route('roulette.result') }}";
                    }, 5000);
                } catch (error) {
                    console.error(error);
                    spinBtn.disabled = false;
                    spinBtn.innerText = "Tenter ma chance";
                    modalTitle.innerText = "Erreur";
                    modalMessage.innerText = error.message || "Une erreur est survenue. Veuillez réessayer.";
                    resultModal.show();
                }
            });
        });
    </script>
@endsection
