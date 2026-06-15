@extends('layouts.app2')

@section('content')
    <style>
        @media (min-width: 1024px) {
            body:has(.sorting2-result) .main-container .main-content {
                margin-left: 0 !important;
                margin-right: 0 !important;
            }
        }

        @property --sorting-angle {
            syntax: '<angle>';
            inherits: false;
            initial-value: 0deg;
        }

        .sorting2-result {
            width: min(1180px, calc(100vw - 48px));
            min-height: calc(100vh - 48px);
            display: grid;
            place-items: center;
            margin: 0 auto;
            padding: 24px;
            color: #172033;
            background:
                linear-gradient(135deg, rgba(248, 250, 252, 0.98), rgba(225, 245, 254, 0.9) 50%, rgba(240, 253, 244, 0.96)),
                url("{{ asset('assets/images/knowledge/pattern.png') }}");
            background-size: cover, 420px;
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 8px;
        }

        .sorting2-result-panel {
            position: relative;
            width: min(820px, 100%);
            overflow: hidden;
            border: 1px solid rgba(15, 23, 42, 0.1);
            border-radius: 8px;
            background:
                linear-gradient(145deg, rgba(255, 255, 255, 0.96), rgba(248, 250, 252, 0.9)),
                linear-gradient(var(--sorting-angle), transparent, color-mix(in srgb, var(--sorting-accent) 16%, transparent), transparent);
            padding: 32px;
            text-align: center;
            box-shadow: 0 26px 68px rgba(15, 23, 42, 0.18);
            backdrop-filter: blur(14px);
            animation: sorting2PanelSweep 5s linear infinite;
        }

        .sorting2-result-panel::before {
            content: "";
            position: absolute;
            inset: 14px;
            z-index: 0;
            border: 1px solid color-mix(in srgb, var(--sorting-accent) 20%, transparent);
            border-radius: 8px;
            opacity: 0.8;
            pointer-events: none;
        }

        .sorting2-result-panel > * {
            position: relative;
            z-index: 1;
        }

        .sorting2-reveal-stage {
            position: relative;
            width: min(430px, 78vw);
            aspect-ratio: 1;
            display: grid;
            place-items: center;
            margin: 0 auto 22px;
            isolation: isolate;
        }

        .sorting2-result-canvas {
            position: absolute;
            inset: -18%;
            z-index: 0;
            width: 136%;
            height: 136%;
            pointer-events: none;
        }

        .sorting2-rune-ring {
            position: absolute;
            inset: 10%;
            z-index: 1;
            border-radius: 50%;
            background:
                conic-gradient(from var(--sorting-angle), transparent 0 18%, color-mix(in srgb, var(--sorting-accent) 78%, #ffffff) 22%, transparent 30% 54%, color-mix(in srgb, var(--sorting-accent) 55%, #ffffff) 62%, transparent 70% 100%);
            mask: radial-gradient(farthest-side, transparent calc(100% - 3px), #000 calc(100% - 2px));
            opacity: 0.62;
            animation: sorting2Spin 2.8s linear infinite;
        }

        .sorting2-rune-ring.two {
            inset: 21%;
            opacity: 0.46;
            animation-duration: 4.6s;
            animation-direction: reverse;
        }

        .sorting2-rune-ring.three {
            inset: 2%;
            opacity: 0.24;
            animation-duration: 7s;
        }

        .sorting2-sigil-deck {
            position: absolute;
            inset: 6%;
            z-index: 2;
            animation: sorting2Deck 3.4s ease-in-out infinite;
        }

        .sorting2-sigil-deck img {
            position: absolute;
            width: 46px;
            height: 46px;
            object-fit: contain;
            opacity: 0.54;
            filter: saturate(0.88) drop-shadow(0 8px 16px rgba(15, 23, 42, 0.12));
            transform: translate(-50%, -50%);
            transition: opacity 420ms ease, transform 420ms ease;
        }

        .sorting2-sigil-deck img:nth-child(1) {
            left: 50%;
            top: 0;
        }

        .sorting2-sigil-deck img:nth-child(2) {
            left: 98%;
            top: 38%;
        }

        .sorting2-sigil-deck img:nth-child(3) {
            left: 78%;
            top: 92%;
        }

        .sorting2-sigil-deck img:nth-child(4) {
            left: 22%;
            top: 92%;
        }

        .sorting2-sigil-deck img:nth-child(5) {
            left: 2%;
            top: 38%;
        }

        .sorting2-result.is-revealed .sorting2-sigil-deck img {
            opacity: 0;
            transform: translate(-50%, -50%) scale(0.6);
        }

        .sorting2-result-image-wrap {
            position: relative;
            z-index: 3;
            width: 66%;
            aspect-ratio: 1;
            display: grid;
            place-items: center;
            border: 1px solid color-mix(in srgb, var(--sorting-accent) 32%, transparent);
            border-radius: 8px;
            background:
                linear-gradient(145deg, rgba(255, 255, 255, 0.88), rgba(248, 250, 252, 0.74));
            box-shadow: inset 0 0 42px color-mix(in srgb, var(--sorting-accent) 12%, transparent);
        }

        .sorting2-result-image-wrap::before,
        .sorting2-result-image-wrap::after {
            content: "";
            position: absolute;
            inset: -16px;
            border-radius: 8px;
            border: 1px solid color-mix(in srgb, var(--sorting-accent) 26%, transparent);
            opacity: 0;
            transform: scale(0.82);
        }

        .sorting2-result.is-waiting .sorting2-result-image-wrap::before {
            opacity: 0.9;
            animation: sorting2PulseFrame 1.55s ease-in-out infinite;
        }

        .sorting2-result.is-waiting .sorting2-result-image-wrap::after {
            opacity: 0.7;
            animation: sorting2PulseFrame 1.55s 0.34s ease-in-out infinite;
        }

        .sorting2-result-image {
            width: 88%;
            height: 88%;
            object-fit: contain;
            opacity: 0;
            transform: scale(0.72) rotate(-8deg);
            filter: blur(16px) saturate(0.6) drop-shadow(0 18px 32px rgba(15, 23, 42, 0.18));
        }

        .sorting2-result.is-revealed .sorting2-result-image {
            animation: sorting2FinalReveal 960ms cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .sorting2-result-kicker {
            display: inline-flex;
            align-items: center;
            min-height: 34px;
            border-radius: 8px;
            background: color-mix(in srgb, var(--sorting-accent) 10%, #ffffff);
            padding: 0 14px;
            color: color-mix(in srgb, var(--sorting-accent) 82%, #0f172a);
            font-weight: 900;
            transition: color 240ms ease, background 240ms ease;
        }

        .sorting2-result-title {
            min-height: 58px;
            margin: 18px 0 0;
            font-size: 2.55rem;
            line-height: 1.08;
            font-weight: 900;
            color: #0f172a;
        }

        .sorting2-before-title,
        .sorting2-after-title {
            display: inline-block;
            transition: opacity 420ms ease, transform 420ms ease, filter 420ms ease;
        }

        .sorting2-after-title {
            position: absolute;
            left: 50%;
            opacity: 0;
            transform: translateX(-50%) translateY(14px);
            filter: blur(10px);
            white-space: nowrap;
        }

        .sorting2-result.is-revealed .sorting2-before-title {
            opacity: 0;
            transform: translateY(-10px);
            filter: blur(8px);
        }

        .sorting2-result.is-revealed .sorting2-after-title {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
            filter: blur(0);
        }

        .sorting2-result-team {
            color: var(--sorting-accent);
        }

        .sorting2-result-copy,
        .sorting2-result-actions {
            opacity: 0;
            transform: translateY(12px);
            transition: opacity 420ms ease, transform 420ms ease;
        }

        .sorting2-result.is-revealed .sorting2-result-copy,
        .sorting2-result.is-revealed .sorting2-result-actions {
            opacity: 1;
            transform: translateY(0);
        }

        .sorting2-result-copy {
            max-width: 560px;
            margin: 14px auto 0;
            color: #475569;
            font-size: 1.05rem;
            line-height: 1.65;
        }

        .sorting2-result-actions {
            display: flex;
            justify-content: center;
            margin-top: 24px;
            transition-delay: 220ms;
        }

        .sorting2-result-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            border-radius: 8px;
            background: #0f172a;
            padding: 0 18px;
            color: #ffffff;
            font-weight: 900;
            transition: transform 160ms ease, box-shadow 160ms ease;
        }

        .sorting2-result-button:hover {
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.16);
        }

        @keyframes sorting2PanelSweep {
            to {
                --sorting-angle: 360deg;
            }
        }

        @keyframes sorting2Spin {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes sorting2Deck {
            0%, 100% {
                transform: rotate(0deg) scale(1);
            }
            50% {
                transform: rotate(18deg) scale(0.96);
            }
        }

        @keyframes sorting2PulseFrame {
            0% {
                opacity: 0.86;
                transform: scale(0.82);
            }
            100% {
                opacity: 0;
                transform: scale(1.2);
            }
        }

        @keyframes sorting2FinalReveal {
            0% {
                opacity: 0;
                transform: scale(0.72) rotate(-8deg);
                filter: blur(16px) saturate(0.6) drop-shadow(0 18px 32px rgba(15, 23, 42, 0.18));
            }
            62% {
                opacity: 1;
                transform: scale(1.08) rotate(2deg);
                filter: blur(0) saturate(1.25) drop-shadow(0 28px 44px color-mix(in srgb, var(--sorting-accent) 38%, transparent));
            }
            100% {
                opacity: 1;
                transform: scale(1) rotate(0);
                filter: blur(0) saturate(1.08) drop-shadow(0 22px 34px rgba(15, 23, 42, 0.2));
            }
        }

        @media (max-width: 640px) {
            .sorting2-result {
                width: calc(100vw - 28px);
                padding: 14px;
            }

            .sorting2-result-panel {
                padding: 18px;
            }

            .sorting2-result-title {
                min-height: 92px;
                font-size: 1.85rem;
            }

            .sorting2-after-title {
                width: 100%;
                white-space: normal;
            }

            .sorting2-result-copy {
                font-size: 1rem;
            }

            .sorting2-result-button {
                width: 100%;
            }
        }
    </style>

    <div class="sorting2-result is-waiting" data-sorting-two-result style="--sorting-accent: {{ $accent }};">
        <section class="sorting2-result-panel">
            <div class="sorting2-reveal-stage">
                <canvas class="sorting2-result-canvas" data-sorting-particles></canvas>

                <div class="sorting2-rune-ring"></div>
                <div class="sorting2-rune-ring two"></div>
                <div class="sorting2-rune-ring three"></div>

                <div class="sorting2-sigil-deck" aria-hidden="true">
                    <img src="{{ asset('assets/images/elements/1.png') }}" alt="">
                    <img src="{{ asset('assets/images/elements/2.png') }}" alt="">
                    <img src="{{ asset('assets/images/elements/3.png') }}" alt="">
                    <img src="{{ asset('assets/images/elements/4.png') }}" alt="">
                    <img src="{{ asset('assets/images/elements/5.png') }}" alt="">
                </div>

                <div class="sorting2-result-image-wrap">
                    <img
                        class="sorting2-result-image"
                        src="{{ asset('assets/images/elements/' . $imageId . '.png') }}"
                        alt="{{ $team->name }}"
                    >
                </div>
            </div>

            <div class="sorting2-result-kicker" data-reveal-status>Стихії прислухаються до виборів</div>
            <h1 class="sorting2-result-title">
                <span class="sorting2-before-title">Знак формується</span>
                <span class="sorting2-after-title">Ваша стихія: <span class="sorting2-result-team">{{ $team->name }}</span></span>
            </h1>
            <p class="sorting2-result-copy">Команда вже зафіксована. Можна передавати естафету наступному учаснику.</p>

            <div class="sorting2-result-actions">
                <a class="sorting2-result-button" href="{{ route('sorting2.show') }}">Наступне сортування</a>
            </div>
        </section>
    </div>

    <script>
        (() => {
            const root = document.querySelector('[data-sorting-two-result]');
            const canvas = document.querySelector('[data-sorting-particles]');
            const status = document.querySelector('[data-reveal-status]');

            if (!root || !canvas || !status) {
                return;
            }

            const accent = getComputedStyle(root).getPropertyValue('--sorting-accent').trim() || '#38bdf8';
            const context = canvas.getContext('2d');
            const steps = [
                'Стихії прислухаються до виборів',
                'Відлуння відповідей збирається',
                'Знак набирає форму',
                'Ще мить'
            ];
            const particles = [];
            let width = 0;
            let height = 0;
            let stepIndex = 0;

            const resize = () => {
                const rect = canvas.getBoundingClientRect();
                const ratio = window.devicePixelRatio || 1;
                width = rect.width;
                height = rect.height;
                canvas.width = Math.max(1, Math.floor(width * ratio));
                canvas.height = Math.max(1, Math.floor(height * ratio));
                context.setTransform(ratio, 0, 0, ratio, 0, 0);
            };

            const randomBetween = (min, max) => min + Math.random() * (max - min);

            const addParticle = (burst = false) => {
                const centerX = width / 2;
                const centerY = height / 2;
                const angle = randomBetween(0, Math.PI * 2);
                const distance = burst ? randomBetween(18, 42) : randomBetween(90, Math.min(width, height) / 2);
                const speed = burst ? randomBetween(1.6, 4.4) : randomBetween(0.16, 0.46);

                particles.push({
                    x: centerX + Math.cos(angle) * distance,
                    y: centerY + Math.sin(angle) * distance,
                    vx: Math.cos(angle) * speed,
                    vy: Math.sin(angle) * speed,
                    size: burst ? randomBetween(2.2, 4.6) : randomBetween(1, 2.4),
                    life: burst ? randomBetween(54, 82) : randomBetween(90, 150),
                    maxLife: burst ? 82 : 150,
                    spin: randomBetween(-0.08, 0.08),
                    angle
                });
            };

            const draw = () => {
                context.clearRect(0, 0, width, height);

                if (particles.length < 64 && !root.classList.contains('is-revealed')) {
                    addParticle(false);
                }

                for (let index = particles.length - 1; index >= 0; index--) {
                    const particle = particles[index];
                    particle.x += particle.vx;
                    particle.y += particle.vy;
                    particle.vx *= 0.992;
                    particle.vy *= 0.992;
                    particle.angle += particle.spin;
                    particle.life -= 1;

                    const alpha = Math.max(0, particle.life / particle.maxLife);
                    context.save();
                    context.translate(particle.x, particle.y);
                    context.rotate(particle.angle);
                    context.globalAlpha = Math.min(0.9, alpha);
                    context.fillStyle = accent;
                    context.fillRect(-particle.size / 2, -particle.size / 2, particle.size, particle.size);
                    context.restore();

                    if (particle.life <= 0) {
                        particles.splice(index, 1);
                    }
                }

                requestAnimationFrame(draw);
            };

            const stepTimer = window.setInterval(() => {
                stepIndex = Math.min(stepIndex + 1, steps.length - 1);
                status.textContent = steps[stepIndex];
            }, 820);

            const reveal = () => {
                window.clearInterval(stepTimer);
                status.textContent = 'Стихія проявилась';
                root.classList.remove('is-waiting');
                root.classList.add('is-revealed');

                for (let index = 0; index < 110; index++) {
                    addParticle(true);
                }
            };

            resize();
            draw();
            window.addEventListener('resize', resize);
            window.setTimeout(reveal, 3400);
        })();
    </script>
@endsection
