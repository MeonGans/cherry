@php
    $prizeWord = match ($selectedCount) {
        1 => 'приз',
        3 => 'призи',
        default => 'призів',
    };
    $arrowLabel = $selectedCount === 1 ? 'Ваш приз' : 'Ваші призи';
    $firstArrowSlot = $arrowSlotIndexes[0] ?? intdiv($visibleSlots, 2);
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
            font-family: Arial, sans-serif;
            letter-spacing: 0;
        }

        button,
        a {
            font: inherit;
        }

        .fortune2 {
            --accent: #4f46e5;
            --accent-2: #12b9c9;
            --warning: #ffcf5a;
            --danger: #eb3349;
            --visible-slots: 7;
            --spin-duration: 11.8s;
            position: relative;
            min-height: 100vh;
            display: grid;
            grid-template-rows: auto 1fr auto;
            gap: 18px;
            padding: clamp(18px, 3vw, 34px);
            isolation: isolate;
            overflow: hidden;
        }

        .fortune2::before {
            content: "";
            position: absolute;
            inset: 0;
            z-index: -3;
            background:
                linear-gradient(135deg, rgba(235, 51, 73, 0.2), transparent 36%),
                linear-gradient(315deg, rgba(18, 185, 201, 0.22), transparent 40%),
                linear-gradient(180deg, #111319, #08090b 72%);
        }

        .fortune2::after {
            content: "";
            position: absolute;
            inset: 0;
            z-index: -2;
            opacity: 0.24;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.08) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.08) 1px, transparent 1px);
            background-size: 42px 42px;
            mask-image: linear-gradient(to bottom, transparent, #000 14%, #000 86%, transparent);
        }

        .fortune2-canvas {
            position: fixed;
            inset: 0;
            z-index: 30;
            pointer-events: none;
        }

        .fortune2-header {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 18px;
        }

        .fortune2-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
            color: var(--warning);
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .fortune2-kicker::before {
            content: "";
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--accent-2);
            box-shadow: 0 0 18px rgba(18, 185, 201, 0.9);
        }

        .fortune2-title {
            margin: 0;
            color: #ffffff;
            font-size: clamp(36px, 6vw, 78px);
            line-height: 0.94;
            font-weight: 950;
        }

        .fortune2-subtitle {
            max-width: 620px;
            margin: 14px 0 0;
            color: rgba(255, 255, 255, 0.72);
            font-size: clamp(15px, 1.7vw, 19px);
            line-height: 1.45;
        }

        .fortune2-status {
            max-width: 420px;
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

        .fortune2-stage {
            position: relative;
            z-index: 2;
            display: grid;
            align-content: center;
            min-height: 460px;
        }

        .fortune2-arrow-title {
            width: fit-content;
            margin: 0 auto;
            padding: 10px 24px;
            border-radius: 8px 8px 0 0;
            color: #ffffff;
            background: var(--accent);
            font-weight: 800;
            text-align: center;
            box-shadow: 0 -12px 36px rgba(79, 70, 229, 0.24);
        }

        .fortune2-reel-wrap {
            position: relative;
            width: min(100%, 1280px);
            margin: 0 auto;
            overflow: hidden;
            border-top: 8px solid var(--accent);
            border-bottom: 8px solid var(--accent);
            background: var(--accent);
            box-shadow:
                0 28px 80px rgba(0, 0, 0, 0.44),
                0 0 0 1px rgba(255, 255, 255, 0.12);
        }

        .fortune2-reel-wrap::before,
        .fortune2-reel-wrap::after {
            content: "";
            position: absolute;
            top: 0;
            z-index: 12;
            width: calc(100% / var(--visible-slots));
            height: 100%;
            pointer-events: none;
        }

        .fortune2-reel-wrap::before {
            left: 0;
            background: linear-gradient(to right, #08090b 12%, transparent);
        }

        .fortune2-reel-wrap::after {
            right: 0;
            background: linear-gradient(to left, #08090b 12%, transparent);
        }

        .fortune2-arrows {
            position: absolute;
            inset: 0;
            z-index: 16;
            pointer-events: none;
        }

        .fortune2-arrow {
            position: absolute;
            top: 0;
            bottom: 0;
            left: var(--arrow-left);
            width: 5px;
            transform: translateX(-50%);
            background: #ffffff;
            box-shadow:
                0 0 0 2px rgba(79, 70, 229, 0.72),
                0 0 26px rgba(255, 255, 255, 0.72);
        }

        .fortune2-arrow::before,
        .fortune2-arrow::after {
            content: "";
            position: absolute;
            left: 50%;
            width: 0;
            height: 0;
            transform: translateX(-50%);
            border-left: 18px solid transparent;
            border-right: 18px solid transparent;
        }

        .fortune2-arrow::before {
            top: 0;
            border-top: 30px solid #ffffff;
        }

        .fortune2-arrow::after {
            bottom: 0;
            border-bottom: 30px solid #ffffff;
        }

        .fortune2-reel {
            display: flex;
            margin: 0;
            padding: 0;
            list-style: none;
            transform: translateX(0);
            transition: transform var(--spin-duration) cubic-bezier(0.13, 0.92, 0.13, 1);
            will-change: transform;
        }

        .fortune2-item {
            flex: 0 0 calc(100% / var(--visible-slots));
            position: relative;
            aspect-ratio: 1;
            padding: clamp(8px, 1.3vw, 14px);
        }

        .fortune2-item-inner {
            position: relative;
            width: 100%;
            height: 100%;
            overflow: hidden;
            border-radius: 8px;
            background: #ffffff;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.32);
        }

        .fortune2-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .fortune2-item span {
            position: absolute;
            left: 8px;
            right: 8px;
            bottom: 8px;
            min-height: 34px;
            display: grid;
            align-items: center;
            padding: 7px 8px;
            border-radius: 8px;
            color: #ffffff;
            background: rgba(8, 9, 11, 0.76);
            font-size: clamp(11px, 1.25vw, 15px);
            font-weight: 800;
            line-height: 1.15;
            text-align: center;
        }

        .fortune2-item.is-winning .fortune2-item-inner {
            outline: 0 solid rgba(255, 207, 90, 0);
        }

        .fortune2.is-revealed .fortune2-item.is-winning .fortune2-item-inner {
            animation: winner-glow 1.2s ease-out forwards;
        }

        .fortune2-controls {
            display: flex;
            justify-content: center;
            margin-top: 26px;
        }

        .fortune2-spin {
            min-width: 210px;
            min-height: 56px;
            border: 0;
            border-radius: 8px;
            color: #ffffff;
            background: var(--accent);
            cursor: pointer;
            font-size: 18px;
            font-weight: 900;
            text-transform: uppercase;
            box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.72);
            animation: spin-pulse 1.6s infinite;
        }

        .fortune2-spin:disabled {
            cursor: not-allowed;
            opacity: 0.6;
            animation: none;
        }

        .fortune2-result {
            position: relative;
            z-index: 2;
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 18px;
            align-items: end;
        }

        .fortune2-prizes {
            display: grid;
            grid-template-columns: repeat(var(--prize-count), minmax(110px, 180px));
            gap: 12px;
            align-items: stretch;
            opacity: 0;
            transform: translateY(18px);
            pointer-events: none;
        }

        .fortune2.is-revealed .fortune2-prizes {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
            transition: opacity 360ms ease, transform 360ms ease;
        }

        .fortune2-prize {
            position: relative;
            min-height: 168px;
            padding: 10px;
            overflow: hidden;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 18px 30px rgba(0, 0, 0, 0.24);
        }

        .fortune2-prize img {
            width: 100%;
            aspect-ratio: 1;
            object-fit: cover;
            border-radius: 8px;
            background: #ffffff;
        }

        .fortune2-prize h2 {
            margin: 10px 0 0;
            color: #ffffff;
            font-size: 15px;
            line-height: 1.2;
        }

        .fortune2-prize small {
            display: block;
            margin-top: 5px;
            color: rgba(255, 255, 255, 0.62);
        }

        .fortune2-claim {
            display: flex;
            align-items: center;
            gap: 12px;
            opacity: 0;
            transform: translateY(18px);
            pointer-events: none;
        }

        .fortune2.is-revealed .fortune2-claim {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
            transition: opacity 360ms ease, transform 360ms ease;
        }

        .fortune2-claim button {
            min-height: 52px;
            padding: 0 22px;
            border: 0;
            border-radius: 8px;
            color: #111319;
            background: var(--warning);
            cursor: pointer;
            font-weight: 900;
            box-shadow: 0 14px 24px rgba(255, 207, 90, 0.3);
        }

        .fortune2-claim button:disabled {
            cursor: not-allowed;
            opacity: 0.58;
        }

        .fortune2-claim span {
            max-width: 210px;
            color: rgba(255, 255, 255, 0.68);
            font-size: 14px;
            line-height: 1.35;
        }

        .fortune2-picker {
            position: fixed;
            right: clamp(14px, 2.4vw, 28px);
            bottom: clamp(14px, 2.4vw, 28px);
            z-index: 40;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px;
            border-radius: 8px;
            background: rgba(8, 9, 11, 0.8);
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

        @keyframes spin-pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.72);
            }

            70% {
                box-shadow: 0 0 0 14px rgba(79, 70, 229, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(79, 70, 229, 0);
            }
        }

        @keyframes winner-glow {
            0% {
                outline-width: 0;
                transform: scale(1);
                box-shadow: 0 0 0 rgba(255, 207, 90, 0);
            }

            44% {
                outline-width: 8px;
                transform: scale(1.04);
                box-shadow: 0 0 44px rgba(255, 207, 90, 0.9);
            }

            100% {
                outline-width: 4px;
                outline-color: rgba(255, 207, 90, 0.92);
                transform: scale(1);
                box-shadow: 0 0 34px rgba(255, 207, 90, 0.48);
            }
        }

        @media (max-width: 900px) {
            .fortune2 {
                padding-bottom: 108px;
            }

            .fortune2-header,
            .fortune2-result {
                grid-template-columns: 1fr;
                display: grid;
            }

            .fortune2-stage {
                min-height: 360px;
            }

            .fortune2-prizes {
                grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
            }
        }

        @media (max-width: 640px) {
            .fortune2 {
                --visible-slots: 3;
                padding: 16px 12px 112px;
            }

            .fortune2-title {
                font-size: 38px;
            }

            .fortune2-arrow-title {
                padding: 8px 18px;
            }

            .fortune2-item span {
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
    data-visible-slots="{{ $visibleSlots }}"
    data-first-arrow-slot="{{ $firstArrowSlot }}"
    data-target-start-index="{{ $targetStartIndex }}"
    style="--visible-slots: {{ $visibleSlots }}; --prize-count: {{ max($selectedCount, 1) }};"
>
    <canvas class="fortune2-canvas" data-confetti></canvas>
    <audio src="{{ asset('fort/audio/onion-capers-by-kevin-macleod-from-filmmusic-io.mp3') }}" data-audio preload="auto"></audio>

    <header class="fortune2-header">
        <div>
            <div class="fortune2-kicker">Cherry Camp Prize Reel</div>
            <h1 class="fortune2-title">Колесо фортуни 2.0</h1>
            <p class="fortune2-subtitle">
                {{ $selectedCount }} {{ $prizeWord }} у цьому запуску. Стрічка зупиниться рівно на тих товарах, які можна забрати.
            </p>
        </div>

        <div>
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
        </div>
    </header>

    <section class="fortune2-stage" aria-label="Прокрут призів">
        <div class="fortune2-arrow-title">{{ $arrowLabel }}</div>
        <div class="fortune2-reel-wrap" data-reel-wrap>
            <div class="fortune2-arrows" aria-hidden="true">
                @foreach($arrowSlotIndexes as $slot)
                    <span
                        class="fortune2-arrow"
                        style="--arrow-left: {{ (($slot + 0.5) / $visibleSlots) * 100 }}%;"
                    ></span>
                @endforeach
            </div>
            <ul class="fortune2-reel" data-reel>
                @foreach($reelItems as $index => $item)
                    <li class="fortune2-item {{ $index >= $targetStartIndex && $index < ($targetStartIndex + $selectedCount) ? 'is-winning' : '' }}">
                        <div class="fortune2-item-inner">
                            <img src="{{ $item['image_url'] }}" alt="{{ $item['name'] }}">
                            <span>{{ $item['name'] }}</span>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="fortune2-controls">
            <button type="button" class="fortune2-spin" data-spin {{ $canSpin ? '' : 'disabled' }}>
                Крутити
            </button>
        </div>
    </section>

    <section class="fortune2-result" aria-live="polite">
        @if($prizes->isNotEmpty())
            <div class="fortune2-prizes">
                @foreach($prizes as $prize)
                    <article class="fortune2-prize">
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
                <span>Після натискання кожен приз спишеться з бази на 1.</span>
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
    const reelWrap = document.querySelector('[data-reel-wrap]');
    const reel = document.querySelector('[data-reel]');
    const spinButton = document.querySelector('[data-spin]');
    const claimButton = document.querySelector('[data-claim]');
    const audio = document.querySelector('[data-audio]');
    const canvas = document.querySelector('[data-confetti]');
    const ctx = canvas.getContext('2d');
    const canSpin = app.dataset.canSpin === '1';
    const visibleSlots = Number(app.dataset.visibleSlots || 7);
    const firstArrowSlot = Number(app.dataset.firstArrowSlot || 3);
    const targetStartIndex = Number(app.dataset.targetStartIndex || 0);
    const spinDurationSeconds = 11.8;
    const colors = ['#eb3349', '#12b9c9', '#ffcf5a', '#ffffff', '#6ee7b7', '#f97316'];
    const particles = [];
    let audioFadeTimer = null;

    function sizeCanvas() {
        canvas.width = window.innerWidth * window.devicePixelRatio;
        canvas.height = window.innerHeight * window.devicePixelRatio;
        ctx.setTransform(window.devicePixelRatio, 0, 0, window.devicePixelRatio, 0, 0);
    }

    function targetTranslateX() {
        const slotWidth = reelWrap.getBoundingClientRect().width / visibleSlots;

        return (firstArrowSlot - targetStartIndex) * slotWidth;
    }

    function playAudio() {
        if (!audio) {
            return;
        }

        window.clearInterval(audioFadeTimer);
        audio.pause();
        audio.currentTime = 1;
        audio.volume = 1;
        audio.play().catch(() => {});

        const fadeStart = spinDurationSeconds * 0.56;
        const fadeStep = 0.04;

        window.setTimeout(() => {
            audioFadeTimer = window.setInterval(() => {
                audio.volume = Math.max(0, audio.volume - fadeStep);

                if (audio.volume <= 0) {
                    window.clearInterval(audioFadeTimer);
                    audio.pause();
                    audio.currentTime = 0;
                }
            }, 140);
        }, fadeStart * 1000);
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
        spinButton.disabled = true;

        if (claimButton) {
            claimButton.disabled = true;
        }

        playAudio();
        burst(80, window.innerWidth * 0.5, window.innerHeight * 0.45);

        requestAnimationFrame(() => {
            reel.style.transform = `translateX(${targetTranslateX()}px)`;
        });

        window.setTimeout(() => {
            app.classList.add('is-revealed');
            burst(160, window.innerWidth * 0.5, window.innerHeight * 0.5);

            if (claimButton) {
                claimButton.disabled = false;
            }
        }, spinDurationSeconds * 1000);
    }

    window.addEventListener('resize', () => {
        sizeCanvas();

        if (app.classList.contains('is-revealed')) {
            reel.style.transition = 'none';
            reel.style.transform = `translateX(${targetTranslateX()}px)`;
            reel.offsetHeight;
            reel.style.transition = '';
        }
    });

    spinButton?.addEventListener('click', spin);
    sizeCanvas();
    tick();
</script>
</body>
</html>
