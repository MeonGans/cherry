@php
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
            overflow: hidden;
            background: #07080d;
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
            --accent-2: #00d4ff;
            --gold: #ffcf5a;
            --hot: #ff3d68;
            --visible-slots: 7;
            --spin-duration: 11.8s;
            position: relative;
            min-height: 100vh;
            isolation: isolate;
            overflow: hidden;
            background:
                radial-gradient(circle at 50% 36%, rgba(0, 212, 255, 0.22), transparent 24%),
                radial-gradient(circle at 18% 18%, rgba(255, 61, 104, 0.2), transparent 26%),
                radial-gradient(circle at 78% 82%, rgba(255, 207, 90, 0.15), transparent 24%),
                linear-gradient(180deg, #121421, #07080d 72%);
        }

        .fortune2::before {
            content: "";
            position: absolute;
            inset: 0;
            z-index: -2;
            opacity: 0.22;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.08) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.08) 1px, transparent 1px);
            background-size: 46px 46px;
            mask-image: radial-gradient(circle at center, #000, transparent 76%);
        }

        .fortune2::after {
            content: "";
            position: absolute;
            inset: 0;
            z-index: -1;
            background:
                linear-gradient(to right, rgba(7, 8, 13, 0.82), transparent 22%, transparent 78%, rgba(7, 8, 13, 0.82)),
                linear-gradient(to bottom, rgba(7, 8, 13, 0.86), transparent 24%, transparent 78%, rgba(7, 8, 13, 0.86));
            pointer-events: none;
        }

        .fortune2-canvas {
            position: fixed;
            inset: 0;
            z-index: 50;
            pointer-events: none;
        }

        .fortune2-status-zone {
            position: fixed;
            top: clamp(14px, 2vw, 26px);
            left: 50%;
            z-index: 20;
            width: min(92vw, 560px);
            transform: translateX(-50%);
            display: grid;
            gap: 10px;
        }

        .fortune2-status {
            padding: 12px 14px;
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 8px;
            color: #ffffff;
            background: rgba(7, 8, 13, 0.72);
            backdrop-filter: blur(14px);
            text-align: center;
            transition: opacity 320ms ease, transform 320ms ease, visibility 320ms ease;
        }

        .fortune2-status.is-success {
            border-color: rgba(58, 213, 133, 0.46);
            color: #a8ffc8;
        }

        .fortune2-status.is-hidden {
            opacity: 0;
            transform: translateY(-10px);
            visibility: hidden;
        }

        .fortune2-stage {
            position: absolute;
            inset: 0;
            z-index: 5;
            display: grid;
            place-items: center;
            padding: clamp(16px, 4vw, 48px) 0;
            transition: opacity 540ms ease, transform 700ms ease, filter 700ms ease;
        }

        .fortune2.is-revealed .fortune2-stage {
            opacity: 0;
            transform: translateY(-58px) scale(0.94);
            filter: blur(8px);
            pointer-events: none;
            transition-delay: 640ms;
        }

        .fortune2-machine {
            position: relative;
            width: 100vw;
            padding: clamp(16px, 2.2vw, 28px) 0;
            border-radius: 0;
            background:
                linear-gradient(145deg, rgba(255, 255, 255, 0.16), rgba(255, 255, 255, 0.03)),
                rgba(16, 18, 28, 0.86);
            border-top: 1px solid rgba(255, 255, 255, 0.18);
            border-bottom: 1px solid rgba(255, 255, 255, 0.18);
            border-left: 0;
            border-right: 0;
            box-shadow:
                0 34px 90px rgba(0, 0, 0, 0.56),
                inset 0 0 0 1px rgba(255, 255, 255, 0.08);
        }

        .fortune2-machine::before,
        .fortune2-machine::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            height: 10px;
            border-radius: 0;
            background: linear-gradient(90deg, transparent, rgba(0, 212, 255, 0.76), rgba(255, 207, 90, 0.76), transparent);
            filter: blur(0.2px);
            opacity: 0.82;
        }

        .fortune2-machine::before {
            top: 9px;
        }

        .fortune2-machine::after {
            bottom: 9px;
        }

        .fortune2-flow {
            position: absolute;
            left: 0;
            right: 0;
            top: 50%;
            height: 64%;
            transform: translateY(-50%);
            overflow: hidden;
            border-radius: 0;
            pointer-events: none;
            opacity: 0.42;
        }

        .fortune2-flow::before,
        .fortune2-flow::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                repeating-linear-gradient(
                    90deg,
                    transparent 0,
                    transparent 22px,
                    rgba(255, 255, 255, 0.12) 22px,
                    rgba(255, 255, 255, 0.12) 26px,
                    transparent 26px,
                    transparent 54px
                );
            transform: translateX(54px);
            animation: flow-right-to-left 1.25s linear infinite;
        }

        .fortune2-flow::after {
            opacity: 0.42;
            animation-duration: 1.9s;
            animation-direction: reverse;
        }

        .fortune2-reel-wrap {
            position: relative;
            width: 100%;
            overflow: hidden;
            border-radius: 0;
            padding: 10px 0;
            background:
                linear-gradient(90deg, rgba(7, 8, 13, 0.95), rgba(25, 29, 45, 0.72), rgba(7, 8, 13, 0.95)),
                #10121c;
            border-top: 1px solid rgba(255, 255, 255, 0.18);
            border-bottom: 1px solid rgba(255, 255, 255, 0.18);
            border-left: 0;
            border-right: 0;
            box-shadow:
                inset 0 0 42px rgba(0, 0, 0, 0.62),
                0 18px 44px rgba(0, 0, 0, 0.36);
        }

        .fortune2-reel-wrap::before,
        .fortune2-reel-wrap::after {
            content: "";
            position: absolute;
            top: 0;
            z-index: 12;
            width: max(12vw, calc(100% / var(--visible-slots)));
            height: 100%;
            pointer-events: none;
        }

        .fortune2-reel-wrap::before {
            left: 0;
            background: linear-gradient(to right, #07080d 16%, transparent);
        }

        .fortune2-reel-wrap::after {
            right: 0;
            background: linear-gradient(to left, #07080d 16%, transparent);
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
            width: 4px;
            transform: translateX(-50%);
            background: #ffffff;
            box-shadow:
                0 0 0 2px rgba(255, 207, 90, 0.74),
                0 0 34px rgba(255, 207, 90, 0.88);
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
            border-top: 30px solid var(--gold);
        }

        .fortune2-arrow::after {
            bottom: 0;
            border-bottom: 30px solid var(--gold);
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
            padding: clamp(7px, 1.1vw, 13px);
        }

        .fortune2-item-inner {
            position: relative;
            width: 100%;
            height: 100%;
            overflow: hidden;
            border-radius: 8px;
            background: #ffffff;
            box-shadow:
                inset 0 0 0 1px rgba(255, 255, 255, 0.34),
                0 12px 26px rgba(0, 0, 0, 0.28);
        }

        .fortune2-item-inner::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(145deg, rgba(255, 255, 255, 0.32), transparent 32%),
                linear-gradient(to top, rgba(0, 0, 0, 0.18), transparent 42%);
            pointer-events: none;
        }

        .fortune2-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scale(1.02);
        }

        .fortune2.is-revealed .fortune2-item.is-winning .fortune2-item-inner {
            animation: winner-lift 980ms cubic-bezier(0.18, 1.25, 0.22, 1) forwards;
        }

        .fortune2-controls {
            display: flex;
            justify-content: center;
            margin-top: 28px;
            padding: 0 16px;
        }

        .fortune2-spin {
            min-width: 210px;
            min-height: 56px;
            border: 0;
            border-radius: 8px;
            color: #10121c;
            background: linear-gradient(135deg, #ffffff, var(--gold));
            cursor: pointer;
            font-size: 18px;
            font-weight: 900;
            text-transform: uppercase;
            box-shadow:
                0 0 0 16px rgba(7, 8, 13, 0.42),
                0 18px 30px rgba(255, 207, 90, 0.24),
                0 0 0 0 rgba(255, 207, 90, 0.6);
            animation: spin-pulse 1.6s infinite;
        }

        .fortune2-spin:disabled {
            cursor: not-allowed;
            opacity: 0.6;
            animation: none;
        }

        .fortune2-result {
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
            transition-delay: 840ms;
        }

        .fortune2.is-revealed .fortune2-result {
            opacity: 1;
            transform: translateY(0) scale(1);
            pointer-events: auto;
        }

        .fortune2-result-inner {
            display: grid;
            justify-items: center;
            gap: 22px;
            width: min(100%, 1180px);
        }

        .fortune2-prizes {
            display: grid;
            grid-template-columns: repeat(var(--prize-count), minmax(130px, 210px));
            justify-content: center;
            gap: clamp(12px, 2vw, 22px);
            width: fit-content;
            max-width: 100%;
        }

        .fortune2-prizes.is-count-1 {
            grid-template-columns: minmax(220px, 320px);
        }

        .fortune2-prizes.is-count-3 {
            grid-template-columns: repeat(3, minmax(170px, 260px));
        }

        .fortune2-prize {
            position: relative;
            min-height: 220px;
            padding: 12px;
            overflow: hidden;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.14), rgba(255, 255, 255, 0.06)),
                rgba(12, 14, 22, 0.88);
            box-shadow:
                0 26px 54px rgba(0, 0, 0, 0.46),
                0 0 0 1px rgba(255, 207, 90, 0.2);
            opacity: 0;
            transform: translateY(70px) scale(0.84);
        }

        .fortune2.is-revealed .fortune2-prize {
            animation: prize-rise 760ms cubic-bezier(0.18, 1.25, 0.22, 1) forwards;
            animation-delay: calc(980ms + var(--i) * 120ms);
        }

        .fortune2-prize::before {
            content: "";
            position: absolute;
            inset: -1px;
            border-radius: inherit;
            background: linear-gradient(135deg, rgba(255, 207, 90, 0.7), transparent 36%, rgba(0, 212, 255, 0.54));
            opacity: 0.28;
            pointer-events: none;
        }

        .fortune2-prize img {
            position: relative;
            z-index: 1;
            width: 100%;
            aspect-ratio: 1;
            object-fit: cover;
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 14px 24px rgba(0, 0, 0, 0.28);
        }

        .fortune2-prize h2 {
            position: relative;
            z-index: 1;
            margin: 12px 0 0;
            color: #ffffff;
            font-size: clamp(15px, 1.5vw, 19px);
            line-height: 1.2;
            text-align: center;
        }

        .fortune2-prize small {
            position: relative;
            z-index: 1;
            display: block;
            margin-top: 7px;
            color: rgba(255, 255, 255, 0.64);
            font-size: 13px;
            text-align: center;
        }

        .fortune2-claim {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 12px;
            opacity: 0;
            transform: translateY(18px);
        }

        .fortune2.is-revealed .fortune2-claim {
            opacity: 1;
            transform: translateY(0);
            transition: opacity 360ms ease, transform 360ms ease;
            transition-delay: 1.3s;
        }

        .fortune2-claim button {
            min-height: 52px;
            padding: 0 24px;
            border: 0;
            border-radius: 8px;
            color: #10121c;
            background: var(--gold);
            cursor: pointer;
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
            line-height: 1.35;
            text-align: center;
        }

        .fortune2-picker {
            position: fixed;
            right: clamp(14px, 2.4vw, 28px);
            bottom: clamp(14px, 2.4vw, 28px);
            z-index: 60;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px;
            border-radius: 8px;
            background: rgba(7, 8, 13, 0.8);
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
            color: #10121c;
            background: #ffffff;
        }

        .fortune2-picker a.is-disabled {
            pointer-events: none;
            opacity: 0.35;
        }

        @keyframes spin-pulse {
            0% {
                box-shadow: 0 0 0 16px rgba(7, 8, 13, 0.42), 0 18px 30px rgba(255, 207, 90, 0.24), 0 0 0 0 rgba(255, 207, 90, 0.6);
            }

            70% {
                box-shadow: 0 0 0 16px rgba(7, 8, 13, 0.42), 0 18px 30px rgba(255, 207, 90, 0.24), 0 0 0 14px rgba(255, 207, 90, 0);
            }

            100% {
                box-shadow: 0 0 0 16px rgba(7, 8, 13, 0.42), 0 18px 30px rgba(255, 207, 90, 0.24), 0 0 0 0 rgba(255, 207, 90, 0);
            }
        }

        @keyframes flow-right-to-left {
            from {
                transform: translateX(54px);
            }

            to {
                transform: translateX(-54px);
            }
        }

        @keyframes winner-lift {
            0% {
                transform: translateY(0) scale(1);
                box-shadow: 0 12px 26px rgba(0, 0, 0, 0.28);
            }

            70% {
                transform: translateY(-42px) scale(1.1);
                box-shadow: 0 0 46px rgba(255, 207, 90, 0.82);
            }

            100% {
                transform: translateY(-28px) scale(1.06);
                box-shadow: 0 0 34px rgba(255, 207, 90, 0.52);
            }
        }

        @keyframes prize-rise {
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
            .fortune2-machine {
                width: 100vw;
            }

            .fortune2-prizes {
                grid-template-columns: repeat(auto-fit, minmax(126px, 1fr));
                width: min(100%, 760px);
            }

            .fortune2-prizes.is-count-1 {
                grid-template-columns: minmax(210px, 320px);
            }

            .fortune2-prizes.is-count-3 {
                grid-template-columns: repeat(3, minmax(140px, 1fr));
            }
        }

        @media (max-width: 640px) {
            .fortune2-stage {
                padding: 12px 0;
            }

            .fortune2-machine {
                padding: 12px 0;
            }

            .fortune2-spin {
                min-width: 170px;
                min-height: 50px;
            }

            .fortune2-prize {
                min-height: 0;
            }

            .fortune2-prizes.is-count-3 {
                grid-template-columns: repeat(auto-fit, minmax(126px, 1fr));
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

    <div class="fortune2-status-zone">
        @if(session('success'))
            <div class="fortune2-status is-success" data-auto-hide>{{ session('success') }}</div>
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

    <section class="fortune2-stage" aria-label="Прокрут призів">
        <div class="fortune2-machine">
            <div class="fortune2-flow" aria-hidden="true"></div>
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
        </div>
    </section>

    <section class="fortune2-result" aria-live="polite">
        @if($prizes->isNotEmpty())
            <div class="fortune2-result-inner">
                <div class="fortune2-prizes is-count-{{ $selectedCount }}">
                    @foreach($prizes as $index => $prize)
                        <article class="fortune2-prize" style="--i: {{ $index }};">
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
            </div>
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
    const maxTravelSlots = 30;
    const colors = ['#eb3349', '#12b9c9', '#ffcf5a', '#ffffff', '#6ee7b7', '#f97316'];
    const particles = [];
    let audioFadeTimer = null;

    function sizeCanvas() {
        canvas.width = window.innerWidth * window.devicePixelRatio;
        canvas.height = window.innerHeight * window.devicePixelRatio;
        ctx.setTransform(window.devicePixelRatio, 0, 0, window.devicePixelRatio, 0, 0);
    }

    function currentVisibleSlots() {
        return Number(getComputedStyle(app).getPropertyValue('--visible-slots')) || visibleSlots;
    }

    function targetTranslateX() {
        const slots = currentVisibleSlots();
        const slotWidth = reelWrap.getBoundingClientRect().width / slots;

        return (firstArrowSlot - targetStartIndex) * slotWidth;
    }

    function travelSlotCount() {
        const items = reel.children.length;
        const availableTail = items - targetStartIndex - currentVisibleSlots() - 2;

        return Math.max(12, Math.min(maxTravelSlots, availableTail));
    }

    function startTranslateX() {
        const slots = currentVisibleSlots();
        const slotWidth = reelWrap.getBoundingClientRect().width / slots;

        return targetTranslateX() + (travelSlotCount() * slotWidth);
    }

    function setReelToStart() {
        reel.style.transition = 'none';
        reel.style.transform = `translateX(${startTranslateX()}px)`;
        reel.offsetHeight;
        reel.style.transition = '';
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
            burst(190, window.innerWidth * 0.5, window.innerHeight * 0.5);

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
        } else {
            setReelToStart();
        }
    });

    spinButton?.addEventListener('click', spin);
    document.querySelectorAll('[data-auto-hide]').forEach((element) => {
        window.setTimeout(() => {
            element.classList.add('is-hidden');
        }, 3500);

        window.setTimeout(() => {
            element.remove();
        }, 3900);
    });
    sizeCanvas();
    setReelToStart();
    tick();
</script>
</body>
</html>
