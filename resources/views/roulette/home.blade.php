@extends('index')
@section('contain')

<div class="roulette-page-wrapper">

  <div class="login-hero">
    <div class="login-hero-avatar">
      <i class="bi bi-trophy" style="font-size: 1.8rem; color: #fff;"></i>
    </div>
    <h1>Tentez votre chance</h1>
    <p>Faites tourner la roue et découvrez votre lot</p>
  </div>

  <div class="roulette-stage">
    <div class="roulette-pointer"></div>
    <div class="roulette-ring"></div>
    <canvas id="wheel" width="300" height="300" class="roulette-wheel"></canvas>
    <div class="roulette-hub"></div>
  </div>

  <p class="spin-hint" id="spin-hint">1 participation disponible</p>

  <button id="spin-btn" class="spin-cta">
    <i class="bi bi-arrow-repeat"></i>
    Tenter ma chance
  </button>

  <div class="lots-legend" id="lots-legend"></div>

  <p class="login-copyright">© 2025 EPA — Accès sécurisé</p>
</div>

<!-- Modal Résultat -->
<div class="modal fade" id="resultModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow">
      <div class="modal-header border-0 pb-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body text-center pb-5 px-4">
        <h2 id="modal-title" class="mb-3" style="color: #00347d; font-weight: 700;"></h2>
        <div id="modal-message" class="fs-5"></div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const wheel     = document.getElementById('wheel');
  const spinBtn   = document.getElementById('spin-btn');
  const spinHint  = document.getElementById('spin-hint');
  const legend    = document.getElementById('lots-legend');
  const ctx       = wheel.getContext('2d');
  const assetBase = "{{ asset('images') }}";
  const resultModal = new bootstrap.Modal(document.getElementById('resultModal'));
  const modalTitle  = document.getElementById('modal-title');
  const modalMessage = document.getElementById('modal-message');

  @if (Auth::user()->played_games)
    spinBtn.disabled = true;
    spinBtn.innerHTML = '<i class="bi bi-check-circle"></i> Vous avez déjà joué';
    spinHint.textContent = 'Participation déjà utilisée';
  @endif

  let segments = @json($gifts);
  if (segments.length === 0) {
    segments = [
      { name: "T-shirt" }, { name: "Casquette" }, { name: "Panier" },
      { name: "Bloc note" }, { name: "Bol" }, { name: "Perdu" }
    ];
  } else {
    segments.push({ name: "Perdu", image: null });
  }

  const palette = ['#FFC107','#4CAF50','#2196F3','#9C27B0','#FF5722','#795548','#607D8B'];
  const cx = 150, cy = 150, r = 148;
  let currentRot = 0;

  function resolveImageUrl(path) {
    if (!path) return null;
    if (/^(https?:)?\/\//.test(path) || path.startsWith('/')) return path;
    return `${assetBase}/${path}`;
  }

  function drawWheel(rotation) {
    ctx.clearRect(0, 0, 300, 300);
    const arc = (2 * Math.PI) / segments.length;
    segments.forEach((seg, i) => {
      const a = rotation + i * arc - Math.PI / 2;
      ctx.beginPath();
      ctx.fillStyle = seg.name === 'Perdu' ? '#680202' : palette[i % palette.length];
      ctx.moveTo(cx, cy);
      ctx.arc(cx, cy, r, a, a + arc);
      ctx.lineTo(cx, cy);
      ctx.fill();
      ctx.lineWidth = 3;
      ctx.strokeStyle = '#fff';
      ctx.stroke();
      ctx.save();
      ctx.translate(cx, cy);
      ctx.rotate(a + arc / 2);
      ctx.textAlign = 'right';
      if (seg.imgObj) {
        ctx.drawImage(seg.imgObj, r - 80, -30, 60, 60);
      } else {
        ctx.fillStyle = '#fff';
        ctx.font = 'bold 13px Arial';
        ctx.shadowColor = 'rgba(0,0,0,0.4)';
        ctx.shadowBlur = 3;
        ctx.fillText(seg.name, r - 14, 5);
      }
      ctx.restore();
    });
  }

  function buildLegend() {
    legend.innerHTML = segments.map((seg, i) => {
      const color = seg.name === 'Perdu' ? '#680202' : palette[i % palette.length];
      return `<div class="legend-pill">
        <span class="legend-dot" style="background:${color}"></span>
        <span>${seg.name}</span>
      </div>`;
    }).join('');
  }

  function loadAndDraw() {
    const promises = segments.map(seg => {
      const url = resolveImageUrl(seg.image);
      if (!url) return Promise.resolve();
      return new Promise(resolve => {
        const img = new Image();
        img.src = url;
        img.onload = () => { seg.imgObj = img; resolve(); };
        img.onerror = resolve;
      });
    });
    Promise.all(promises).then(() => { drawWheel(0); buildLegend(); });
  }

  function rotationForIndex(index) {
    const seg = 360 / segments.length;
    return ((360 - (index * seg + seg / 2)) % 360) * Math.PI / 180;
  }

  function fireConfetti() {
    confetti({ particleCount: 150, spread: 70, origin: { y: 0.6 }, zIndex: 2000 });
  }

  loadAndDraw();

  spinBtn.addEventListener('click', async function () {
    spinBtn.disabled = true;
    spinHint.textContent = 'Bonne chance !';

    try {
      const response = await fetch('{{ url('/save-game-result') }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
      });
      const data = await response.json();
      if (!response.ok || !data.success) throw new Error(data.message || 'Erreur lors du tirage.');

      const prizeName  = data.prize || 'Perdu';
      const prizeIndex = segments.findIndex(s => s.name === prizeName);
      const landIndex  = prizeIndex >= 0 ? prizeIndex : segments.findIndex(s => s.name === 'Perdu');
      const spins      = Math.floor(Math.random() * 5) + 5;
      const target     = rotationForIndex(landIndex >= 0 ? landIndex : 0);

      const totalRot = spins * 2 * Math.PI + target - (currentRot % (2 * Math.PI));
      const duration = 5000;
      const startTime = performance.now();
      const startRot  = currentRot;

      function animate(now) {
        const t    = Math.min((now - startTime) / duration, 1);
        const ease = 1 - Math.pow(1 - t, 4);
        currentRot = startRot + totalRot * ease;
        drawWheel(currentRot);
        if (t < 1) { requestAnimationFrame(animate); return; }
        setTimeout(() => {
          window.location.href = data.redirect || "{{ route('roulette.result') }}";
        }, 500);
      }
      requestAnimationFrame(animate);

    } catch (error) {
      spinBtn.disabled = false;
      spinHint.textContent = '1 participation disponible';
      modalTitle.innerText  = 'Erreur';
      modalMessage.innerText = error.message || 'Une erreur est survenue.';
      resultModal.show();
    }
  });
});
</script>
@endsection