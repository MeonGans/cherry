@php
    $itemsCount = max($wheelItems->count(), 1);
    $prizeWord = match ($selectedCount) {
        1 => 'приз',
        3 => 'призи',
        default => 'призів',
    };
@endphp
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Колесо фортуни 2.0</title>
    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            min-height: 100%;
        }

        body {
            margin: 0;
            overflow-x: hidden;
            background: #08090b;
            color: #ffffff;
            font-family: Inter, Arial, sans-serif;
            letter-spacing: 0;
        }

        button,
        a {
            font: inherit;
        }

        .fortune2 {
            position: relative;
            min-height: 100vh;
            display: grid;
            grid-template-columns: minmax(0, 1.08fr) minmax(320px, 0.92fr);
            gap: clamp(24px, 4vw, 56px);
            align-items: center;
            padding: clamp(20px, 4vw, 54px);
            isolation: isolate;
            overflow: hidden;
        }

        .fortune2::before {
            content: "";
            position: absolute;
            inset: 0;
            z-index: -3;
            background:
                linear-gradient(135deg, rgba(235, 51, 73, 0.22), transparent 34%),
                linear-gradient(315deg, rgba(17, 185, 201, 0.2), transparent 42%),
                #08090b;
        }

        .fortune2::after {
            content: "";
            position: absolute;
            inset: 0;
            z-index: -2;
            opacity: 0.32;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.08) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.08) 1px, transparent 1px);
            background-size: 44px 44px;
            mask-image: linear-gradient(to bottom, transparent, #000 16%, #000 84%, transparent);
        }

        .fortune2-canvas {
            position: fixed;
            inset: 0;
            z-index: 5;
            pointer-events: none;
        }

        .fortune2-stage {
            position: relative;
            display: grid;
            place-items: center;
            min-height: min(78vh, 760px);
        }

        .fortune2-stage::before {
            content: "";
            position: absolute;
            width: min(80vw, 760px);
            aspect-ratio: 1;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, 0.12);
            transform: scale(1);
            animation: pulse-ring 2.4s ease-in-out infinite;
        }

        .fortune2-wheel-shell {
            --item-radius: min(27vw, 236px);
            position: relative;
            width: min(68vw, 640px);
            max-width: 78vh;
            aspect-ratio: 1;
        }

        .fortune2-pointer {
            position: absolute;
            left: 50%;
            top: -12px;
            z-index: 8;
            width: 0;
            height: 0;
            transform: translateX(-50%);
            border-left: 20px solid transparent;
            border-right: 20px solid transparent;
            border-top: 42px solid #ffffff;
            filter: drop-shadow(0 6px 18px rgba(255, 255, 255, 0.55));
        }

        .fortune2-wheel {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: clamp(10px, 2vw, 18px) solid rgba(255, 255, 255, 0.92);
            box-shadow:
                0 28px 70px rgba(0, 0, 0, 0.52),
                0 0 0 12px rgba(17, 185, 201, 0.16),
                inset 0 0 42px rgba(0, 0, 0, 0.46);
            transition: transform 6.1s cubic-bezier(0.12, 0.72, 0.04, 1);
            transform: rotate(0deg);
            overflow: hidden;
        }

        .fortune2-wheel::before {
            content: "";
            position: absolute;
            inset: 12%;
            border-radius: 50%;
            background:
                linear-gradient(145deg, rgba(255, 255, 255, 0.38), rgba(255, 255, 255, 0.04)),
                #101217;
            box-shadow:
                inset 0 0 30px rgba(255, 255, 255, 0.12),
                0 0 0 1px rgba(255, 255, 255, 0.26);
        }

        .fortune2-wheel::after {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: 50%;
            background:
                radial-gradient(circle at 38% 28%, rgba(255, 255, 255, 0.32), transparent 20%),
                radial-gradient(circle, transparent 54%, rgba(0, 0, 0, 0.3) 100%);
            mix-blend-mode: screen;
            pointer-events: none;
        }

        .fortune2.is-spinning .fortune2-wheel {
            transform: rotate(var(--spin-rotation, 2520deg));
        }

        .fortune2-product {
            position: absolute;
            left: 50%;
            top: 50%;
            z-index: 3;
            width: clamp(58px, 8vw, 86px);
            min-height: clamp(74px, 10vw, 106px);
            display: grid;
            justify-items: center;
            gap: 5px;
            padding: 7px;
            border-radius: 8px;
            background: rgba(8, 9, 11, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.22);
            box-shadow: 0 12px 22px rgba(0, 0, 0, 0.24);
            transform: translate(-50%, -50%) rotate(var(--angle)) translateY(calc(-1 * var(--item-radius))) rotate(var(--counter-angle));
            transform-origin: center;
        }

        .fortune2-product img {
            width: clamp(38px, 6vw, 58px);
            height: clamp(38px, 6vw, 58px);
            object-fit: cover;
            border-radius: 6px;
            background: #ffffff;
        }

        .fortune2-product span {
            max-width: 100%;
            color: #ffffff;
            font-size: clamp(9px, 1.2vw, 11px);
            line-height: 1.1;
            text-align: center;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .fortune2-hub {
            position: absolute;
            left: 50%;
            top: 50%;
            z-index: 9;
            width: clamp(132px, 18vw, 184px);
            aspect-ratio: 1;
            display: grid;
            place-items: center;
            transform: translate(-50%, -50%);
            border-radius: 50%;
            background:
                linear-gradient(145deg, #ffffff, #d8f9fb 48%, #ffd15c);
            border: 8px solid #111319;
            box-shadow:
                0 0 0 1px rgba(255, 255, 255, 0.48),
                0 22px 42px rgba(0, 0, 0, 0.34);
        }

        .fortune2-spin {
            width: calc(100% - 28px);
            aspect-ratio: 1;
            border: 0;
            border-radius: 50%;
            color: #0c1016;
            background: transparent;
            font-weight: 900;
            font-size: clamp(16px, 2.2vw, 24px);
            text-transform: uppercase;
        }

        .fortune2-spin:disabled {
            cursor: not-allowed;
            opacity: 0.54;
        }

        .fortune2-panel {
            position: relative;
            z-index: 2;
            align-self: center;
            max-width: 650px;
        }

        .fortune2-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 14px;
            color: #ffcf5a;
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .fortune2-kicker::before {
            content: "";
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #12b9c9;
            box-shadow: 0 0 18px rgba(18, 185, 201, 0.9);
        }

        .fortune2-title {
            margin: 0;
            color: #ffffff;
            font-size: clamp(42px, 6vw, 86px);
            line-height: 0.95;
            font-weight: 950;
        }

        .fortune2-subtitle {
            max-width: 560px;
            margin: 18px 0 0;
            color: rgba(255, 255, 255, 0.72);
            font-size: clamp(16px, 1.8vw, 20px);
            line-height: 1.45;
        }

        .fortune2-status {
            margin-top: 18px;
            padding: 12px 14px;
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 8px;
            color: #ffffff;
            background: rgba(255, 255, 255, 0.08);
        }

        .fortune2-status.is-success {
            border-color: rgba(58, 213, 133, 0.46);
            color: #a8ffc8;
        }

        .fortune2-result {
            margin-top: clamp(22px, 4vw, 38px);
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(132px, 1fr));
            gap: 14px;
        }

        .fortune2-prize {
            position: relative;
            min-height: 184px;
            padding: 14px;
            border-radius: 8px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 0 18px 30px rgba(0, 0, 0, 0.28);
            opacity: 0;
            transform: translateY(28px) scale(0.92) rotate(-2deg);
        }

        .fortune2-prize::before {
            content: "";
            position: absolute;
            inset: -1px;
            border-radius: inherit;
            border: 1px solid rgba(255, 209, 92, 0.46);
            opacity: 0;
        }

        .fortune2.is-revealed .fortune2-prize {
            animation: prize-pop 620ms cubic-bezier(0.2, 1.2, 0.2, 1) forwards;
            animation-delay: var(--delay);
        }

        .fortune2.is-revealed .fortune2-prize::before {
            animation: prize-flash 900ms ease-out forwards;
            animation-delay: var(--delay);
        }

        .fortune2-prize img {
            width: 100%;
            aspect-ratio: 1;
            object-fit: cover;
            border-radius: 8px;
            background: #ffffff;
        }

        .fortune2-prize h2 {
            margin: 12px 0 0;
            font-size: 16px;
            line-height: 1.25;
        }

        .fortune2-prize small {
            display: block;
            margin-top: 6px;
            color: rgba(255, 255, 255, 0.58);
        }

        .fortune2-claim {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 12px;
            margin-top: 20px;
            opacity: 0;
            transform: translateY(12px);
            pointer-events: none;
        }

        .fortune2.is-revealed .fortune2-claim {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
            transition: opacity 360ms ease, transform 360ms ease;
        }

        .fortune2-claim button {
            min-height: 48px;
            padding: 0 22px;
            border: 0;
            border-radius: 8px;
            color: #111319;
            background: #ffcf5a;
            font-weight: 900;
            box-shadow: 0 14px 24px rgba(255, 207, 90, 0.3);
        }

        .fortune2-claim button:disabled {
            cursor: not-allowed;
            opacity: 0.58;
        }

        .fortune2-claim span {
            color: rgba(255, 255, 255, 0.68);
            font-size: 14px;
        }

        .fortune2-picker {
            position: fixed;
            right: clamp(14px, 2.4vw, 28px);
            bottom: clamp(14px, 2.4vw, 28px);
            z-index: 12;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px;
            border-radius: 8px;
            background: rgba(8, 9, 11, 0.78);
            border: 1px solid rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(14px);
            box-shadow: 0 18px 42px rgba(0, 0, 0, 0.34);
        }

        .fortune2-picker span {
            padding: 0 8px;
            color: rgba(255, 255, 255, 0.64);
            font-size: 13px;
            white-space: nowrap;
        }

        .fortune2-picker a {
            min-width: 42px;
            min-height: 42px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            color: #ffffff;
            text-decoration: none;
            border: 1px solid rgba(255, 255, 255, 0.14);
            background: rgba(255, 255, 255, 0.08);
            font-weight: 900;
        }

        .fortune2-picker a.is-active {
            color: #111319;
            background: #ffffff;
        }

        .fortune2-picker a.is-disabled {
            pointer-events: none;
            opacity: 0.35;
        }

        @keyframes pulse-ring {
            0%,
            100% {
                opacity: 0.14;
                transform: scale(0.96);
            }

            50% {
                opacity: 0.48;
                transform: scale(1.03);
            }
        }

        @keyframes prize-pop {
            0% {
                opacity: 0;
                transform: translateY(28px) scale(0.92) rotate(-2deg);
            }

            70% {
                opacity: 1;
                transform: translateY(-6px) scale(1.04) rotate(1deg);
            }

            100% {
                opacity: 1;
                transform: translateY(0) scale(1) rotate(0);
            }
        }

        @keyframes prize-flash {
            0% {
                opacity: 0;
                transform: scale(0.92);
            }

            34% {
                opacity: 1;
            }

            100% {
                opacity: 0;
                transform: scale(1.08);
            }
        }

        @media (max-width: 1020px) {
            .fortune2 {
                grid-template-columns: 1fr;
                padding-bottom: 108px;
            }

            .fortune2-panel {
                max-width: none;
            }

            .fortune2-wheel-shell {
                width: min(88vw, 560px);
                --item-radius: min(35vw, 204px);
            }
        }

        @media (max-width: 640px) {
            .fortune2 {
                padding: 18px 14px 112px;
            }

            .fortune2-stage {
                min-height: 54vh;
            }

            .fortune2-product {
                width: 52px;
                min-height: 66px;
                padding: 5px;
            }

            .fortune2-product span {
                display: none;
            }

            .fortune2-picker {
                left: 50%;
                right: auto;
                transform: translateX(-50%);
            }
        }
    </style>
</head>
<body>
<main
    class="fortune2"
    data-fortune-two
    data-selected-count="{{ $selectedCount }}"
    data-can-spin="{{ $canSpin ? '1' : '0' }}"
>
    <canvas class="fortune2-canvas" data-confetti></canvas>

    <section class="fortune2-stage" aria-label="Колесо фортуни 2.0">
        <div class="fortune2-wheel-shell">
            <div class="fortune2-pointer"></div>
            <div class="fortune2-wheel" data-wheel>
                @foreach($wheelItems as $index => $item)
                    @php
                        $angle = (360 / $itemsCount) * $index;
                    @endphp
                    <div
                        class="fortune2-product"
                        style="--angle: {{ $angle }}deg; --counter-angle: -{{ $angle }}deg;"
                    >
                        <img src="{{ $item['image_url'] }}" alt="{{ $item['name'] }}">
                        <span>{{ $item['name'] }}</span>
                    </div>
                @endforeach
            </div>
            <div class="fortune2-hub">
                <button type="button" class="fortune2-spin" data-spin {{ $canSpin ? '' : 'disabled' }}>
                    Старт
                </button>
            </div>
        </div>
    </section>

    <section class="fortune2-panel">
        <div class="fortune2-kicker">Cherry Camp Prize Drop</div>
        <h1 class="fortune2-title">Колесо фортуни 2.0</h1>
        <p class="fortune2-subtitle">
            {{ $selectedCount }} {{ $prizeWord }} у цьому запуску.
        </p>

        @if(session('success'))
            <div class="fortune2-status is-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="fortune2-status">{{ $errors->first() }}</div>
        @endif

        @if(!$canSpin)
            <div class="fortune2-status">
                Доступно різних призів: {{ $availableUnique }}. Для цього режиму потрібно {{ $selectedCount }}.
            </div>
        @endif

        @if($prizes->isNotEmpty())
            <div class="fortune2-result" aria-live="polite">
                @foreach($prizes as $index => $prize)
                    <article class="fortune2-prize" style="--delay: {{ 160 + ($index * 140) }}ms;">
                        <img src="{{ $prize['image_url'] }}" alt="{{ $prize['name'] }}">
                        <h2>{{ $prize['name'] }}</h2>
                        <small>Залишок до списання: {{ $prize['quantity'] }}</small>
                    </article>
                @endforeach
            </div>

            <form action="{{ route('fortune.two.catch') }}" method="POST" class="fortune2-claim">
                @csrf
                @foreach($prizes as $prize)
                    <input type="hidden" name="product_ids[]" value="{{ $prize['id'] }}">
                @endforeach
                <button type="submit" data-claim disabled>Забрати приз</button>
                <span>Списання відбудеться після натискання.</span>
            </form>
        @endif
    </section>

    <nav class="fortune2-picker" aria-label="Кількість призів">
        <span>Призів</span>
        @foreach($allowedCounts as $count)
            @php
                $isAvailable = $availableCounts->contains($count);
            @endphp
            <a
                href="{{ route('fortune.two', ['prizes' => $count]) }}"
                class="{{ $selectedCount === $count ? 'is-active' : '' }} {{ $isAvailable ? '' : 'is-disabled' }}"
                aria-disabled="{{ $isAvailable ? 'false' : 'true' }}"
            >
                {{ $count }}
            </a>
        @endforeach
    </nav>
</main>

<script>
    const app = document.querySelector('[data-fortune-two]');
    const wheel = document.querySelector('[data-wheel]');
    const spinButton = document.querySelector('[data-spin]');
    const claimButton = document.querySelector('[data-claim]');
    const canvas = document.querySelector('[data-confetti]');
    const ctx = canvas.getContext('2d');
    const selectedCount = Number(app.dataset.selectedCount || 1);
    const canSpin = app.dataset.canSpin === '1';
    const wheelItems = @json($wheelItems);
    const colors = ['#eb3349', '#12b9c9', '#ffcf5a', '#ffffff', '#6ee7b7', '#f97316'];
    const particles = [];

    function sizeCanvas() {
        canvas.width = window.innerWidth * window.devicePixelRatio;
        canvas.height = window.innerHeight * window.devicePixelRatio;
        ctx.setTransform(window.devicePixelRatio, 0, 0, window.devicePixelRatio, 0, 0);
    }

    function paintWheel() {
        const count = Math.max(wheelItems.length, 1);
        const segment = 360 / count;
        const stops = [];

        for (let i = 0; i < count; i++) {
            const start = i * segment;
            const end = (i + 1) * segment;
            stops.push(`${colors[i % colors.length]} ${start}deg ${end}deg`);
        }

        wheel.style.background = `conic-gradient(from -90deg, ${stops.join(',')})`;
    }

    function burst(amount, x = window.innerWidth / 2, y = window.innerHeight / 2) {
        for (let i = 0; i < amount; i++) {
            const angle = Math.random() * Math.PI * 2;
            const speed = 2 + Math.random() * 7;

            particles.push({
                x,
                y,
                vx: Math.cos(angle) * speed,
                vy: Math.sin(angle) * speed - Math.random() * 2,
                life: 0,
                ttl: 70 + Math.random() * 46,
                size: 3 + Math.random() * 5,
                color: colors[Math.floor(Math.random() * colors.length)],
                gravity: 0.07 + Math.random() * 0.08,
            });
        }
    }

    function tick() {
        ctx.clearRect(0, 0, window.innerWidth, window.innerHeight);

        for (let i = particles.length - 1; i >= 0; i--) {
            const p = particles[i];
            p.life += 1;
            p.x += p.vx;
            p.y += p.vy;
            p.vy += p.gravity;
            p.vx *= 0.986;

            const alpha = Math.max(0, 1 - p.life / p.ttl);
            ctx.globalAlpha = alpha;
            ctx.fillStyle = p.color;
            ctx.fillRect(p.x, p.y, p.size, p.size * 1.7);

            if (p.life >= p.ttl) {
                particles.splice(i, 1);
            }
        }

        ctx.globalAlpha = 1;
        requestAnimationFrame(tick);
    }

    function spin() {
        if (!canSpin || !spinButton || spinButton.disabled) {
            return;
        }

        app.classList.remove('is-revealed');
        app.classList.add('is-spinning');
        spinButton.disabled = true;

        if (claimButton) {
            claimButton.disabled = true;
        }

        const rotation = (360 * (7 + selectedCount)) + Math.floor(Math.random() * 360);
        wheel.style.setProperty('--spin-rotation', `${rotation}deg`);
        burst(80, window.innerWidth * 0.38, window.innerHeight * 0.48);

        setTimeout(() => {
            app.classList.add('is-revealed');
            burst(130 + selectedCount * 28, window.innerWidth * 0.72, window.innerHeight * 0.46);

            if (claimButton) {
                claimButton.disabled = false;
            }
        }, 6200);
    }

    window.addEventListener('resize', sizeCanvas);
    spinButton?.addEventListener('click', spin);

    sizeCanvas();
    paintWheel();
    tick();
</script>
</body>
</html>
