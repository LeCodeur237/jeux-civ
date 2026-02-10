@extends('index')

@section('contain')
    <div class="scratch-page"
        style="background-image: linear-gradient(180deg, rgba(63, 0, 8, 0.92), rgba(20, 0, 4, 0.75)), url('{{ asset('images/51941.jpg') }}');">
        <div class="container scratch-layout">
            <div class="scratch-header">
                <form action="{{ url('/logout') }}" method="POST" style="display: inline;" onsubmit="localStorage.removeItem('player_id');">
                    @csrf
                    <button type="submit" class="btn-back-floating" style="border: none;">
                        <i class="bi bi-box-arrow-left"></i>
                    </button>
                </form>
                <div class="app-title">
                    <h1>Jeu à gratter</h1>
                </div>
            </div>
            <div class="scratch-subtitle">
                Une surprise d'amour vous attend. Grattez et decouvrez votre lot.
            </div>
            {{-- <div class="player-position" id="player-position"></div> --}}

            <div class="scratch-card">
                <div class="scratch-prize" id="prize-text"></div>
                <img id="prize-image" class="scratch-prize-image" src="" alt="Lot gagne">
                <div class="scratch-congrats-text" id="congrats-text"></div>
                <div class="scratch-result-text" id="result-text"></div>
                <div class="reveal-overlay" id="reveal-overlay" aria-live="polite">
                    <div class="reveal-text">Decouvrez votre lot dans</div>
                    <div class="reveal-gift">
                        🎁
                        <span class="reveal-countdown" id="reveal-countdown">3</span>
                    </div>
                </div>
                <div class="win-dots" id="win-dots" aria-hidden="true">
                    <span class="dot dot-1"></span>
                    <span class="dot dot-2"></span>
                    <span class="dot dot-3"></span>
                    <span class="dot dot-4"></span>
                    <span class="dot dot-5"></span>
                    <span class="dot dot-6"></span>
                </div>
                <canvas id="scratch-canvas" class="scratch-canvas" width="300" height="300"
                    aria-label="Zone a gratter"></canvas>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <script>
        function showSnackbar(message) {
            var x = document.getElementById("snackbar");
            if (x) {
                x.innerText = message;
                x.className = "show";
                setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Vérification immédiate de l'existence du joueur
            const storedPlayerId = localStorage.getItem('player_id');
            if (!storedPlayerId) {
                window.location.href = "{{ url('/registering') }}";
                return;
            }

            const prizes = [{
                    label: "Un dîner pour deux au restaurant LE LOF",
                    image: "{{ asset('img/lots-1.jpeg') }}"
                },
                {
                    label: "Un contrat d’assurance MonAPPUI pour deux",
                    image: "{{ asset('img/lots-2.jpeg') }}"
                },
                {
                    label: "Une prestation d’extension de cils de chez ELIAB",
                    image: "{{ asset('img/lots-3.jpeg') }}"
                },
                {
                    label: "Un somptueux bouquet de roses nature",
                    image: "{{ asset('img/lots-4.jpeg') }}"
                }
            ];
            const losePrize = {
                label: "Perdu",
                image: "{{ asset('img/failed.png') }}"
            };

            const canvas = document.getElementById('scratch-canvas');
            const ctx = canvas.getContext('2d');
            const prizeText = document.getElementById('prize-text');
            const resetBtn = document.getElementById('scratch-reset');
            const heartImg = new Image();
            heartImg.onload = () => {
                if (!hasPlayed) drawCover();
            };
            heartImg.src = "{{ asset('img/coeur.png') }}";
            const prizeImage = document.getElementById('prize-image');
            const winDots = document.getElementById('win-dots');
            const revealOverlay = document.getElementById('reveal-overlay');
            const revealCountdown = document.getElementById('reveal-countdown');
            const revealText = document.querySelector('.reveal-text');
            const resultText = document.getElementById('result-text');
            const congratsText = document.getElementById('congrats-text');
            const playerPosition = document.getElementById('player-position');

            let isDrawing = false;
            let revealed = false;
            let revealTimer = null;
            let confettiInterval = null;
            let hasPlayed = false;
            let currentPrize = null;
            let outcomePromise = null;
            let outcomeLabel = null;

            function startGame() {
                if (hasPlayed) return;
                drawCover();
            }

            function showPlayedResult(prizeLabel) {
                hasPlayed = true;
                revealed = true;
                isDrawing = false;
                canvas.style.pointerEvents = 'none';
                canvas.style.display = 'none';
                prizeText.style.display = 'none';
                revealOverlay.style.display = 'none';
                winDots.style.display = 'none';
                if (resetBtn) resetBtn.style.display = 'none';

                let prizeObj = prizes.find(p => p.label === prizeLabel);
                if (!prizeObj) prizeObj = losePrize;

                prizeImage.src = prizeObj.image;
                prizeImage.alt = prizeObj.label;
                prizeImage.classList.remove('is-win');
                prizeImage.classList.remove('is-lose');
                prizeImage.classList.add('is-revealed');
                prizeImage.style.opacity = '1';
                prizeImage.style.visibility = 'visible';
                resultText.style.display = 'block';
                if (congratsText) congratsText.textContent = "";

                if (prizeLabel && prizeLabel !== "Perdu") {
                    prizeImage.classList.add('is-win');
                    resultText.classList.add('is-win');
                    resultText.textContent = "Vous avez déjà participé à ce jeu. Vous avez gagné : " + prizeLabel;
                } else {
                    prizeImage.classList.add('is-lose');
                    resultText.classList.remove('is-win');
                    resultText.textContent = "Vous avez déjà participé à ce jeu. Résultat : Perdu";
                }
            }

            // Contrôle serveur : le joueur a-t-il déjà joué ?
            fetch("{{ url('/check-player-status') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                body: JSON.stringify({ player_id: storedPlayerId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.has_played) {
                    showPlayedResult(data.prize);
                } else {
                    if (playerPosition && data.position) {
                        playerPosition.textContent = `Position joueur : ${data.position}`;
                    }
                    startGame();
                }
            })
            .catch(error => {
                console.error('Erreur lors de la vérification du statut:', error);
                startGame();
            });

            function setPrize(prizeLabel) {
                let prize = prizes.find(p => p.label === prizeLabel);
                if (!prize) prize = losePrize;
                prizeImage.src = prize.image;
                prizeImage.alt = prize.label;
                prizeImage.classList.remove('is-win');
                prizeImage.classList.remove('is-lose');
                prizeImage.classList.remove('is-revealed');
                winDots.classList.remove('is-win');
                revealOverlay.classList.remove('is-active');
                resultText.classList.remove('is-win');
                resultText.textContent = "";
                if (congratsText) congratsText.textContent = "";
                if (congratsText) congratsText.textContent = "";
            }

            function drawCover() {
                ctx.globalCompositeOperation = 'source-over';
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, canvas.width, canvas.height);

                if (heartImg.complete) {
                    const size = Math.min(canvas.width, canvas.height) * 0.9;
                    const x = (canvas.width - size) / 2;
                    const y = (canvas.height - size) / 2;
                    ctx.drawImage(heartImg, x, y, size, size);
                }

                ctx.globalCompositeOperation = 'destination-out';
            }

            function getPointerPos(e) {
                const rect = canvas.getBoundingClientRect();
                const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                const clientY = e.touches ? e.touches[0].clientY : e.clientY;
                return {
                    x: clientX - rect.left,
                    y: clientY - rect.top
                };
            }

            function scratch(e) {
                if (!isDrawing) return;
                const pos = getPointerPos(e);
                ctx.beginPath();
                ctx.arc(pos.x, pos.y, 18, 0, Math.PI * 2);
                ctx.fill();
                // Dès le premier grattage, on demande le résultat au serveur
                if (!outcomePromise) {
                    const playerId = localStorage.getItem('player_id');
                    if (playerId) {
                        outcomePromise = fetch("{{ url('/save-player-game-result') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({
                                player_id: playerId
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            outcomeLabel = data.prize || "Perdu";
                            if (outcomeLabel !== "Perdu") {
                                if (congratsText) congratsText.textContent = "Félicitations, vous avez gagné 🎊";
                            }
                            if (data.success) {
                                if (resetBtn) resetBtn.style.display = 'none';
                            } else {
                                showSnackbar(data.message || "Vous avez déjà joué.");
                                if (resetBtn) resetBtn.style.display = 'none';
                            }
                            return data;
                        })
                        .catch(error => console.error('Erreur:', error));
                    }
                }
                checkReveal();
            }

            function checkReveal() {
                if (revealed) return;
                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const data = imageData.data;
                let transparent = 0;
                for (let i = 3; i < data.length; i += 4) {
                    if (data[i] === 0) transparent++;
                }
                const percent = transparent / (data.length / 4);
                if (percent > 0.40) {
                    revealed = true;
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    prizeText.textContent = "";
                    canvas.style.pointerEvents = 'none';

                    // On utilise le résultat déjà demandé au premier grattage
                    const applyOutcome = (prizeLabel) => {
                        setPrize(prizeLabel);

                        if (prizeLabel === "Perdu") {
                            // Affichage direct sans compte à rebours
                            resultText.classList.remove('is-win');
                            prizeImage.classList.add('is-revealed');
                            prizeImage.classList.add('is-lose');
                            resultText.textContent = "Pas de chance cette fois, merci d’avoir participé";
                            if (congratsText) congratsText.textContent = "";
                            if (confettiInterval) {
                                clearInterval(confettiInterval);
                                confettiInterval = null;
                            }
                        } else {
                            // Compte à rebours uniquement pour un gain
                            if (congratsText) congratsText.textContent = "Félicitations, vous avez gagné 🎊";
                            let remaining = 3;

                            if (revealTimer) clearInterval(revealTimer);
                            setTimeout(() => {
                                if (revealText) revealText.textContent = "Decouvrez votre lot dans";
                                revealCountdown.textContent = remaining;
                                revealOverlay.classList.add('is-active');
                                revealTimer = setInterval(() => {
                                    remaining -= 1;
                                    revealCountdown.textContent = remaining;
                                    if (remaining <= 0) {
                                        clearInterval(revealTimer);
                                        revealTimer = null;
                                        revealOverlay.classList.remove('is-active');

                                        prizeImage.classList.add('is-revealed');
                                        prizeImage.classList.add('is-win');
                                        winDots.classList.add('is-win');
                                        resultText.textContent = `Bravo ! Vous avez gagne : ${prizeLabel}.`;
                                        if (congratsText) congratsText.textContent = "";

                                        const fireConfetti = () => {
                                            confetti({
                                                particleCount: 140,
                                                spread: 80,
                                                origin: {
                                                    y: 0.6
                                                },
                                                zIndex: 2000
                                            });
                                        };
                                        fireConfetti();
                                        if (confettiInterval) clearInterval(confettiInterval);
                                        confettiInterval = setInterval(fireConfetti, 10000);
                                    }
                                }, 1000);
                            }, 400);
                        }
                    };

                    if (outcomeLabel) {
                        applyOutcome(outcomeLabel);
                    } else if (outcomePromise) {
                        outcomePromise.then(data => {
                            const prizeLabel = (data && data.prize) ? data.prize : "Perdu";
                            applyOutcome(prizeLabel);
                        });
                    }
                }
            }

            function startScratch(e) {
                isDrawing = true;
                scratch(e);
            }

            function endScratch() {
                isDrawing = false;
            }

            function resetScratch() {
                if (hasPlayed) return;
                revealed = false;
                prizeText.textContent = "Grattez le coeur";
                prizeImage.classList.remove('is-revealed');
                prizeImage.classList.remove('is-win');
                prizeImage.classList.remove('is-lose');
                winDots.classList.remove('is-win');
                revealOverlay.classList.remove('is-active');
                resultText.classList.remove('is-win');
                resultText.textContent = "";
                canvas.style.pointerEvents = 'auto';
                if (revealTimer) {
                    clearInterval(revealTimer);
                    revealTimer = null;
                }
                if (confettiInterval) {
                    clearInterval(confettiInterval);
                    confettiInterval = null;
                }
                outcomePromise = null;
                outcomeLabel = null;
                setPrize("Perdu");
                drawCover();
            }

            canvas.addEventListener('mousedown', startScratch);
            canvas.addEventListener('mousemove', scratch);
            canvas.addEventListener('mouseup', endScratch);
            canvas.addEventListener('mouseleave', endScratch);
            canvas.addEventListener('touchstart', startScratch, {
                passive: true
            });
            canvas.addEventListener('touchmove', scratch, {
                passive: true
            });
            canvas.addEventListener('touchend', endScratch);

            if (resetBtn) resetBtn.addEventListener('click', resetScratch);
        });
    </script>
@endsection
