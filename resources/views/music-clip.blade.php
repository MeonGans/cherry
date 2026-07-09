<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Музичний кліп</title>
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
            overflow: hidden;
            background: #07070b;
            color: #ffffff;
            font-family: Arial, sans-serif;
            letter-spacing: 0;
        }

        button {
            font: inherit;
        }

        .music-clip {
            --visible-slots: 7;
            --spin-duration: 10.8s;
            --gold: #ffd15c;
            --cyan: #1ed7ff;
            --rose: #ff3f7a;
            --green: #6ee7b7;
            position: relative;
            min-height: 100vh;
            overflow: hidden;
            isolation: isolate;
            background:
                radial-gradient(circle at 22% 18%, rgba(255, 63, 122, 0.24), transparent 25%),
                radial-gradient(circle at 76% 20%, rgba(30, 215, 255, 0.2), transparent 25%),
                radial-gradient(circle at 56% 84%, rgba(255, 209, 92, 0.16), transparent 28%),
                linear-gradient(180deg, #121320, #07070b 72%);
        }

        .music-clip::before {
            content: "";
            position: absolute;
            inset: 0;
            z-index: -2;
            opacity: 0.24;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.08) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.08) 1px, transparent 1px);
            background-size: 44px 44px;
            mask-image: radial-gradient(circle at center, #000, transparent 78%);
        }

        .music-clip::after {
            content: "";
            position: absolute;
            inset: 0;
            z-index: -1;
            background:
                linear-gradient(to right, rgba(7, 7, 11, 0.88), transparent 18%, transparent 82%, rgba(7, 7, 11, 0.88)),
                linear-gradient(to bottom, rgba(7, 7, 11, 0.9), transparent 24%, transparent 78%, rgba(7, 7, 11, 0.9));
            pointer-events: none;
        }

        .clip-confetti {
            position: fixed;
            inset: 0;
            z-index: 50;
            pointer-events: none;
        }

        .clip-topbar {
            position: fixed;
            top: clamp(14px, 2vw, 26px);
            left: 50%;
            z-index: 20;
            display: flex;
            align-items: center;
            gap: 12px;
            width: min(92vw, 760px);
            transform: translateX(-50%);
            justify-content: center;
            pointer-events: none;
        }

        .clip-title {
            margin: 0;
            color: #ffffff;
            font-size: clamp(24px, 3vw, 48px);
            line-height: 1;
            text-transform: none;
            text-shadow:
                0 0 24px rgba(30, 215, 255, 0.52),
                0 0 42px rgba(255, 63, 122, 0.32);
        }

        .clip-status-zone {
            position: fixed;
            top: clamp(74px, 8vw, 96px);
            left: 50%;
            z-index: 25;
            width: min(92vw, 560px);
            display: grid;
            gap: 10px;
            transform: translateX(-50%);
        }

        .clip-status {
            padding: 12px 14px;
            border-radius: 8px;
            color: #ffffff;
            background: rgba(7, 7, 11, 0.76);
            border: 1px solid rgba(255, 255, 255, 0.16);
            backdrop-filter: blur(14px);
            text-align: center;
            transition: opacity 320ms ease, transform 320ms ease, visibility 320ms ease;
        }

        .clip-status.is-success {
            border-color: rgba(110, 231, 183, 0.42);
            color: #b7ffe3;
        }

        .clip-status.is-hidden {
            opacity: 0;
            transform: translateY(-10px);
            visibility: hidden;
        }

        .clip-stage {
            position: absolute;
            inset: 0;
            z-index: 5;
            display: grid;
            align-content: center;
            gap: clamp(18px, 3vw, 34px);
            padding: clamp(80px, 11vh, 120px) 0 clamp(42px, 7vh, 80px);
            transition: opacity 560ms ease, transform 700ms ease, filter 700ms ease;
        }

        .music-clip.is-revealed .clip-stage {
            opacity: 0;
            transform: scale(0.95);
            filter: blur(8px);
            pointer-events: none;
            transition-delay: 520ms;
        }

        .clip-machine {
            position: relative;
            width: 100vw;
            display: grid;
            gap: clamp(16px, 2.5vw, 28px);
        }

        .clip-row {
            position: relative;
            width: 100vw;
            padding: clamp(10px, 1.6vw, 16px) 0;
            background:
                linear-gradient(90deg, rgba(7, 7, 11, 0.96), rgba(22, 26, 42, 0.78), rgba(7, 7, 11, 0.96)),
                #10121c;
            border-top: 1px solid rgba(255, 255, 255, 0.18);
            border-bottom: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow:
                inset 0 0 48px rgba(0, 0, 0, 0.6),
                0 22px 54px rgba(0, 0, 0, 0.34);
        }

        .clip-row::before,
        .clip-row::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            height: 8px;
            background: linear-gradient(90deg, transparent, var(--cyan), var(--gold), var(--rose), transparent);
            opacity: 0.75;
            pointer-events: none;
        }

        .clip-row::before {
            top: -1px;
        }

        .clip-row::after {
            bottom: -1px;
        }

        .clip-flow {
            position: absolute;
            inset: 12px 0;
            overflow: hidden;
            opacity: 0.36;
            pointer-events: none;
        }

        .clip-flow::before,
        .clip-flow::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                repeating-linear-gradient(
                    90deg,
                    transparent 0,
                    transparent 24px,
                    rgba(255, 255, 255, 0.12) 24px,
                    rgba(255, 255, 255, 0.12) 28px,
                    transparent 28px,
                    transparent 58px
                );
            animation: clip-flow-left 1.22s linear infinite;
        }

        .clip-row.is-bottom .clip-flow::before,
        .clip-row.is-bottom .clip-flow::after {
            animation-name: clip-flow-right;
        }

        .clip-flow::after {
            opacity: 0.42;
            animation-duration: 1.8s;
            animation-direction: reverse;
        }

        .clip-reel-wrap {
            position: relative;
            width: 100vw;
            overflow: hidden;
            padding: 0;
        }

        .clip-reel-wrap::before,
        .clip-reel-wrap::after {
            content: "";
            position: absolute;
            top: 0;
            z-index: 12;
            width: max(12vw, calc(100% / var(--visible-slots)));
            height: 100%;
            pointer-events: none;
        }

        .clip-reel-wrap::before {
            left: 0;
            background: linear-gradient(to right, #07070b 18%, transparent);
        }

        .clip-reel-wrap::after {
            right: 0;
            background: linear-gradient(to left, #07070b 18%, transparent);
        }

        .clip-pointer {
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            z-index: 16;
            width: 4px;
            transform: translateX(-50%);
            background: #ffffff;
            box-shadow:
                0 0 0 2px rgba(255, 209, 92, 0.74),
                0 0 34px rgba(255, 209, 92, 0.88);
            pointer-events: none;
        }

        .clip-pointer::before,
        .clip-pointer::after {
            content: "";
            position: absolute;
            left: 50%;
            width: 0;
            height: 0;
            transform: translateX(-50%);
            border-left: 18px solid transparent;
            border-right: 18px solid transparent;
        }

        .clip-pointer::before {
            top: 0;
            border-top: 30px solid var(--gold);
        }

        .clip-pointer::after {
            bottom: 0;
            border-bottom: 30px solid var(--gold);
        }

        .clip-reel {
            display: flex;
            margin: 0;
            padding: 0;
            list-style: none;
            transform: translateX(0);
            transition: transform var(--spin-duration) cubic-bezier(0.13, 0.92, 0.13, 1);
            will-change: transform;
        }

        .clip-item {
            flex: 0 0 calc(100% / var(--visible-slots));
            position: relative;
            aspect-ratio: 1;
            padding: clamp(7px, 1.1vw, 13px);
        }

        .clip-item-inner {
            position: relative;
            width: 100%;
            height: 100%;
            overflow: hidden;
            border-radius: 8px;
            background: #ffffff;
            box-shadow:
                inset 0 0 0 1px rgba(255, 255, 255, 0.32),
                0 12px 26px rgba(0, 0, 0, 0.3);
        }

        .clip-item-inner::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(145deg, rgba(255, 255, 255, 0.34), transparent 34%),
                linear-gradient(to top, rgba(0, 0, 0, 0.2), transparent 44%);
            pointer-events: none;
        }

        .clip-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scale(1.02);
        }

        .music-clip.is-revealed .clip-item.is-winning .clip-item-inner {
            animation: clip-winner-lift 920ms cubic-bezier(0.18, 1.25, 0.22, 1) forwards;
        }

        .clip-controls {
            display: flex;
            justify-content: center;
        }

        .clip-start {
            min-width: 210px;
            min-height: 58px;
            border: 0;
            border-radius: 8px;
            color: #10121c;
            background: linear-gradient(135deg, #ffffff, var(--gold));
            cursor: pointer;
            font-size: 18px;
            font-weight: 900;
            text-transform: uppercase;
            box-shadow:
                0 0 0 16px rgba(7, 7, 11, 0.42),
                0 18px 30px rgba(255, 209, 92, 0.25),
                0 0 0 0 rgba(255, 209, 92, 0.6);
            animation: clip-start-pulse 1.6s infinite;
        }

        .clip-start:disabled {
            cursor: not-allowed;
            opacity: 0.6;
            animation: none;
        }

        .clip-result {
            position: absolute;
            inset: 0;
            z-index: 10;
            display: grid;
            place-items: center;
            padding: clamp(18px, 4vw, 54px);
            opacity: 0;
            transform: translateY(34px) scale(0.96);
            pointer-events: none;
            transition: opacity 440ms ease, transform 600ms cubic-bezier(0.18, 1.12, 0.22, 1);
            transition-delay: 720ms;
        }

        .music-clip.is-revealed .clip-result {
            opacity: 1;
            transform: translateY(0) scale(1);
            pointer-events: auto;
        }

        .clip-result-inner {
            display: grid;
            justify-items: center;
            gap: 24px;
            width: min(100%, 860px);
        }

        .clip-result-pair {
            display: grid;
            grid-template-columns: repeat(2, minmax(210px, 320px));
            gap: clamp(14px, 2.4vw, 26px);
            justify-content: center;
            width: fit-content;
            max-width: 100%;
        }

        .clip-result-card {
            position: relative;
            min-height: 300px;
            padding: 12px;
            overflow: hidden;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.14), rgba(255, 255, 255, 0.06)),
                rgba(12, 14, 22, 0.9);
            box-shadow:
                0 26px 54px rgba(0, 0, 0, 0.46),
                0 0 0 1px rgba(255, 209, 92, 0.2);
            opacity: 0;
            transform: translateY(70px) scale(0.84);
        }

        .music-clip.is-revealed .clip-result-card {
            animation: clip-result-rise 760ms cubic-bezier(0.18, 1.25, 0.22, 1) forwards;
            animation-delay: calc(880ms + var(--i) * 130ms);
        }

        .clip-result-card::before {
            content: "";
            position: absolute;
            inset: -1px;
            border-radius: inherit;
            background: linear-gradient(135deg, rgba(255, 209, 92, 0.72), transparent 38%, rgba(30, 215, 255, 0.54));
            opacity: 0.28;
            pointer-events: none;
        }

        .clip-result-card img {
            position: relative;
            z-index: 1;
            width: 100%;
            aspect-ratio: 1;
            object-fit: cover;
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 14px 24px rgba(0, 0, 0, 0.28);
        }

        .clip-result-card span {
            position: relative;
            z-index: 1;
            display: block;
            margin-top: 12px;
            color: rgba(255, 255, 255, 0.62);
            font-size: 13px;
            font-weight: 800;
            text-align: center;
            text-transform: uppercase;
        }

        .clip-result-card h2 {
            position: relative;
            z-index: 1;
            margin: 7px 0 0;
            color: #ffffff;
            font-size: clamp(18px, 2vw, 25px);
            line-height: 1.15;
            text-align: center;
        }

        .clip-result-audio {
            position: relative;
            z-index: 1;
            margin-top: 14px;
            padding: 8px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.14);
        }

        .clip-result-audio audio {
            display: block;
            width: 100%;
            height: 34px;
        }

        .clip-claim {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 12px;
            opacity: 0;
            transform: translateY(18px);
        }

        .music-clip.is-revealed .clip-claim {
            opacity: 1;
            transform: translateY(0);
            transition: opacity 360ms ease, transform 360ms ease;
            transition-delay: 1.25s;
        }

        .clip-claim button {
            min-height: 52px;
            padding: 0 24px;
            border: 0;
            border-radius: 8px;
            color: #10121c;
            background: var(--gold);
            cursor: pointer;
            font-weight: 900;
            box-shadow: 0 14px 24px rgba(255, 209, 92, 0.3);
        }

        .clip-claim button:disabled {
            cursor: not-allowed;
            opacity: 0.58;
        }

        .clip-claim span {
            color: rgba(255, 255, 255, 0.68);
            font-size: 14px;
            line-height: 1.35;
            text-align: center;
        }

        @keyframes clip-flow-left {
            from {
                transform: translateX(58px);
            }

            to {
                transform: translateX(-58px);
            }
        }

        @keyframes clip-flow-right {
            from {
                transform: translateX(-58px);
            }

            to {
                transform: translateX(58px);
            }
        }

        @keyframes clip-start-pulse {
            0% {
                box-shadow: 0 0 0 16px rgba(7, 7, 11, 0.42), 0 18px 30px rgba(255, 209, 92, 0.25), 0 0 0 0 rgba(255, 209, 92, 0.6);
            }

            70% {
                box-shadow: 0 0 0 16px rgba(7, 7, 11, 0.42), 0 18px 30px rgba(255, 209, 92, 0.25), 0 0 0 14px rgba(255, 209, 92, 0);
            }

            100% {
                box-shadow: 0 0 0 16px rgba(7, 7, 11, 0.42), 0 18px 30px rgba(255, 209, 92, 0.25), 0 0 0 0 rgba(255, 209, 92, 0);
            }
        }

        @keyframes clip-winner-lift {
            0% {
                transform: translateY(0) scale(1);
                box-shadow: 0 12px 26px rgba(0, 0, 0, 0.3);
            }

            70% {
                transform: translateY(-34px) scale(1.1);
                box-shadow: 0 0 46px rgba(255, 209, 92, 0.82);
            }

            100% {
                transform: translateY(-22px) scale(1.06);
                box-shadow: 0 0 34px rgba(255, 209, 92, 0.52);
            }
        }

        @keyframes clip-result-rise {
            0% {
                opacity: 0;
                transform: translateY(70px) scale(0.84);
            }

            72% {
                opacity: 1;
                transform: translateY(-10px) scale(1.04);
            }

            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @media (max-width: 900px) {
            .music-clip {
                --visible-slots: 5;
            }

            .clip-result-pair {
                grid-template-columns: repeat(2, minmax(170px, 260px));
            }

            .clip-result-card {
                min-height: 0;
            }
        }

        @media (max-width: 640px) {
            .music-clip {
                --visible-slots: 3;
            }

            .clip-stage {
                padding-top: 92px;
                gap: 14px;
            }

            .clip-start {
                min-width: 168px;
                min-height: 50px;
            }

            .clip-result-pair {
                grid-template-columns: minmax(210px, 320px);
                width: min(100%, 340px);
            }

        }
    </style>
</head>
<body>
<main
    class="music-clip"
    data-music-clip
    data-can-spin="{{ $canSpin ? '1' : '0' }}"
    data-target-index="{{ $targetIndex }}"
>
    <canvas class="clip-confetti" data-confetti></canvas>
    <audio src="{{ asset('fort/audio/onion-capers-by-kevin-macleod-from-filmmusic-io.mp3') }}" data-audio preload="auto"></audio>

    <header class="clip-topbar">
        <h1 class="clip-title">Музичний кліп</h1>
    </header>

    <div class="clip-status-zone">
        @if(session('success'))
            <div class="clip-status is-success" data-auto-hide>{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="clip-status">{{ $errors->first() }}</div>
        @endif

        @if(!$canSpin)
            <div class="clip-status">
                Для старту потрібен хоча б 1 доступний жанр і 1 доступний кліп.
            </div>
        @endif
    </div>

    <section class="clip-stage" aria-label="Прокрут жанру та кліпу">
        <div class="clip-machine">
            <div class="clip-row is-top">
                <div class="clip-flow" aria-hidden="true"></div>
                <div class="clip-reel-wrap" data-reel-wrap>
                    <span class="clip-pointer" aria-hidden="true"></span>
                    <ul class="clip-reel" data-reel data-direction="rtl">
                        @foreach($genreReelItems as $index => $item)
                            <li class="clip-item {{ $index === $targetIndex ? 'is-winning' : '' }}">
                                <div class="clip-item-inner">
                                    <img src="{{ $item['image_url'] }}" alt="{{ $item['name'] }}">
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="clip-controls">
                <button type="button" class="clip-start" data-start {{ $canSpin ? '' : 'disabled' }}>
                    Старт
                </button>
            </div>

            <div class="clip-row is-bottom">
                <div class="clip-flow" aria-hidden="true"></div>
                <div class="clip-reel-wrap" data-reel-wrap>
                    <span class="clip-pointer" aria-hidden="true"></span>
                    <ul class="clip-reel" data-reel data-direction="ltr">
                        @foreach($songReelItems as $index => $item)
                            <li class="clip-item {{ $index === $targetIndex ? 'is-winning' : '' }}">
                                <div class="clip-item-inner">
                                    <img src="{{ $item['image_url'] }}" alt="{{ $item['name'] }}">
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="clip-result" aria-live="polite">
        @if($genre && $song)
            <div class="clip-result-inner">
                <div class="clip-result-pair">
                    <article class="clip-result-card" style="--i: 0;">
                        <img src="{{ $genre['image_url'] }}" alt="{{ $genre['name'] }}">
                        <span>Жанр</span>
                        <h2>{{ $genre['name'] }}</h2>
                    </article>

                    <article class="clip-result-card" style="--i: 1;">
                        <img src="{{ $song['image_url'] }}" alt="{{ $song['name'] }}">
                        <span>Кліп</span>
                        <h2>{{ $song['name'] }}</h2>
                        @if(!empty($song['audio_url']))
                            <div class="clip-result-audio">
                                <audio controls preload="metadata" src="{{ $song['audio_url'] }}"></audio>
                            </div>
                        @endif
                    </article>
                </div>

                <form action="{{ route('music.clip.catch') }}" method="POST" class="clip-claim">
                    @csrf
                    <input type="hidden" name="genre_id" value="{{ $genre['id'] }}">
                    <input type="hidden" name="song_id" value="{{ $song['id'] }}">
                    <button type="submit" data-claim disabled>Забрати кліп</button>
                    <span>Після натискання жанр і кліп стануть недоступними в системі.</span>
                </form>
            </div>
        @endif
    </section>

</main>

<script>
    const app = document.querySelector('[data-music-clip]');
    const reelInfos = Array.from(document.querySelectorAll('[data-reel]')).map((reel) => ({
        reel,
        wrap: reel.closest('[data-reel-wrap]'),
        direction: reel.dataset.direction,
    }));
    const startButton = document.querySelector('[data-start]');
    const claimButton = document.querySelector('[data-claim]');
    const audio = document.querySelector('[data-audio]');
    const canvas = document.querySelector('[data-confetti]');
    const ctx = canvas.getContext('2d');
    const canSpin = app.dataset.canSpin === '1';
    const targetIndex = Number(app.dataset.targetIndex || 0);
    const spinDurationSeconds = 10.8;
    const travelSlots = 30;
    const colors = ['#ff3f7a', '#1ed7ff', '#ffd15c', '#ffffff', '#6ee7b7', '#a78bfa'];
    const particles = [];
    let audioFadeTimer = null;

    function sizeCanvas() {
        canvas.width = window.innerWidth * window.devicePixelRatio;
        canvas.height = window.innerHeight * window.devicePixelRatio;
        ctx.setTransform(window.devicePixelRatio, 0, 0, window.devicePixelRatio, 0, 0);
    }

    function currentVisibleSlots() {
        return Number(getComputedStyle(app).getPropertyValue('--visible-slots')) || 7;
    }

    function slotWidth(info) {
        return info.wrap.getBoundingClientRect().width / currentVisibleSlots();
    }

    function targetTranslateX(info) {
        const centerSlot = Math.floor(currentVisibleSlots() / 2);

        return (centerSlot - targetIndex) * slotWidth(info);
    }

    function startTranslateX(info) {
        const travel = travelSlots * slotWidth(info);

        if (info.direction === 'rtl') {
            return targetTranslateX(info) + travel;
        }

        return targetTranslateX(info) - travel;
    }

    function setReelsToStart() {
        reelInfos.forEach((info) => {
            info.reel.style.transition = 'none';
            info.reel.style.transform = `translateX(${startTranslateX(info)}px)`;
            info.reel.offsetHeight;
            info.reel.style.transition = '';
        });
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

        window.setTimeout(() => {
            audioFadeTimer = window.setInterval(() => {
                audio.volume = Math.max(0, audio.volume - 0.04);

                if (audio.volume <= 0) {
                    window.clearInterval(audioFadeTimer);
                    audio.pause();
                    audio.currentTime = 0;
                }
            }, 140);
        }, spinDurationSeconds * 560);
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
        if (!canSpin || !startButton || startButton.disabled) {
            return;
        }

        app.classList.remove('is-revealed');
        startButton.disabled = true;

        if (claimButton) {
            claimButton.disabled = true;
        }

        playAudio();
        burst(70, window.innerWidth * 0.5, window.innerHeight * 0.48);

        requestAnimationFrame(() => {
            reelInfos.forEach((info) => {
                info.reel.style.transform = `translateX(${targetTranslateX(info)}px)`;
            });
        });

        window.setTimeout(() => {
            app.classList.add('is-revealed');
            burst(190, window.innerWidth * 0.5, window.innerHeight * 0.5);

            if (claimButton) {
                claimButton.disabled = false;
            }
        }, spinDurationSeconds * 1000);
    }

    window.addEventListener('resize', () => {
        sizeCanvas();

        if (app.classList.contains('is-revealed')) {
            reelInfos.forEach((info) => {
                info.reel.style.transition = 'none';
                info.reel.style.transform = `translateX(${targetTranslateX(info)}px)`;
                info.reel.offsetHeight;
                info.reel.style.transition = '';
            });
        } else {
            setReelsToStart();
        }
    });

    startButton?.addEventListener('click', spin);

    document.querySelectorAll('[data-auto-hide]').forEach((element) => {
        window.setTimeout(() => {
            element.classList.add('is-hidden');
        }, 3500);

        window.setTimeout(() => {
            element.remove();
        }, 3900);
    });

    sizeCanvas();
    setReelsToStart();
    tick();
</script>
</body>
</html>
