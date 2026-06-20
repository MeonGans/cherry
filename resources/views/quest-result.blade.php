@extends('layouts.app2')

@section('content')
    <main class="quest-result quest-result--{{ $result['variant'] }}">
        <section class="quest-result-card">
            <div class="quest-result-runes" aria-hidden="true">
                <span>W</span>
                <span>N</span>
                <span>13</span>
            </div>

            <div class="quest-result-visual" aria-hidden="true">
                <div class="quest-safe">
                    <div class="quest-safe-lid"></div>
                    <div class="quest-safe-door">
                        <span></span>
                    </div>
                    <div class="quest-safe-glow"></div>
                </div>
            </div>

            <div class="quest-result-copy">
                <p class="quest-result-eyebrow">{{ $result['eyebrow'] }}</p>
                <h1>{{ $result['label'] }}</h1>
                <p class="quest-result-rank">{{ $result['rank'] }}</p>
                <div class="quest-result-message">
                    {{ $message }}
                </div>
            </div>
        </section>
    </main>

    <style>
        .quest-result {
            --accent: #7d2039;
            --accent-2: #c3415f;
            --box: #5e152a;
            --box-dark: #250911;
            --metal: #d9d3df;
            --paper: #f2eef5;
            min-height: calc(100vh - 48px);
            margin: -1.5rem;
            display: grid;
            place-items: center;
            padding: clamp(18px, 4vw, 64px);
            color: var(--paper);
            background:
                radial-gradient(circle at 50% 28%, color-mix(in srgb, var(--accent) 32%, transparent), transparent 30%),
                radial-gradient(circle at 18% 18%, rgba(76, 42, 122, 0.24), transparent 25%),
                repeating-linear-gradient(90deg, rgba(255, 255, 255, 0.035) 0 1px, transparent 1px 46px),
                linear-gradient(180deg, #15131c, #060609 74%);
            font-family: Georgia, "Times New Roman", serif;
            overflow: hidden;
        }

        .quest-result::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            background:
                linear-gradient(90deg, rgba(0, 0, 0, 0.74), transparent 18%, transparent 82%, rgba(0, 0, 0, 0.74)),
                linear-gradient(180deg, rgba(0, 0, 0, 0.48), transparent 42%, rgba(0, 0, 0, 0.72));
        }

        .quest-result--silver {
            --accent: #8f95a3;
            --accent-2: #ece8ef;
            --box: #b9bbc3;
            --box-dark: #4a4d57;
        }

        .quest-result--black {
            --accent: #2c2835;
            --accent-2: #8e7ca4;
            --box: #14121a;
            --box-dark: #050507;
        }

        .quest-result--bonus,
        .quest-result--bonus-strong {
            --accent: #4c2a7a;
            --accent-2: #b79ade;
            --box: #34204e;
            --box-dark: #120b1d;
        }

        .quest-result--bonus-strong {
            --accent: #5a347f;
            --accent-2: #d4b6ff;
        }

        .quest-result--closed {
            --accent: #36313e;
            --accent-2: #83798e;
            --box: #2a2630;
            --box-dark: #0b0a0d;
        }

        .quest-result-card {
            position: relative;
            z-index: 1;
            width: min(100%, 980px);
            display: grid;
            grid-template-columns: minmax(220px, 0.9fr) minmax(280px, 1.1fr);
            gap: clamp(24px, 5vw, 56px);
            align-items: center;
            padding: clamp(24px, 5vw, 58px);
            border: 1px solid rgba(242, 238, 245, 0.2);
            background:
                linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.03)),
                rgba(9, 8, 13, 0.88);
            box-shadow:
                0 34px 100px rgba(0, 0, 0, 0.7),
                inset 0 0 0 1px rgba(255, 255, 255, 0.05);
        }

        .quest-result-card::before,
        .quest-result-card::after {
            content: "";
            position: absolute;
            left: 24px;
            right: 24px;
            height: 1px;
            background: linear-gradient(90deg, transparent, color-mix(in srgb, var(--accent-2) 64%, transparent), transparent);
        }

        .quest-result-card::before {
            top: 18px;
        }

        .quest-result-card::after {
            bottom: 18px;
        }

        .quest-result-runes {
            position: absolute;
            top: 24px;
            right: 28px;
            display: flex;
            gap: 8px;
            color: rgba(242, 238, 245, 0.32);
            font-size: 12px;
            letter-spacing: 0.16em;
        }

        .quest-result-visual {
            position: relative;
            min-height: 300px;
            display: grid;
            place-items: center;
        }

        .quest-result-visual::before {
            content: "";
            position: absolute;
            width: min(72vw, 360px);
            aspect-ratio: 1;
            border-radius: 50%;
            background: radial-gradient(circle, color-mix(in srgb, var(--accent) 46%, transparent), transparent 68%);
            filter: blur(8px);
            animation: quest-aura 2.8s ease-in-out infinite;
        }

        .quest-safe {
            position: relative;
            z-index: 1;
            width: min(68vw, 280px);
            aspect-ratio: 1.08;
            perspective: 900px;
            filter: drop-shadow(0 28px 34px rgba(0, 0, 0, 0.48));
        }

        .quest-safe-lid {
            position: absolute;
            left: 8%;
            right: 8%;
            top: 6%;
            height: 22%;
            border: 1px solid rgba(255, 255, 255, 0.24);
            border-bottom: 0;
            background:
                linear-gradient(135deg, rgba(255, 255, 255, 0.16), transparent 38%),
                linear-gradient(180deg, var(--box), var(--box-dark));
            transform: rotateX(0deg) translateY(0);
            transform-origin: bottom center;
            animation: quest-lid 1.9s cubic-bezier(0.18, 1.2, 0.22, 1) forwards;
        }

        .quest-safe-door {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 76%;
            display: grid;
            place-items: center;
            border: 1px solid rgba(255, 255, 255, 0.24);
            background:
                linear-gradient(135deg, rgba(255, 255, 255, 0.18), transparent 36%),
                linear-gradient(180deg, var(--box), var(--box-dark));
            box-shadow:
                inset 0 0 0 10px rgba(0, 0, 0, 0.16),
                inset 0 -40px 70px rgba(0, 0, 0, 0.24);
        }

        .quest-safe-door span {
            width: 78px;
            height: 78px;
            border-radius: 50%;
            border: 10px solid color-mix(in srgb, var(--metal) 76%, var(--box));
            background:
                radial-gradient(circle, var(--box-dark) 0 20%, transparent 22%),
                conic-gradient(from 20deg, var(--metal), #67636e, var(--metal), #57525d, var(--metal));
            box-shadow: 0 0 24px color-mix(in srgb, var(--accent-2) 32%, transparent);
        }

        .quest-safe-glow {
            position: absolute;
            left: 12%;
            right: 12%;
            top: 26%;
            height: 22%;
            background: linear-gradient(90deg, transparent, color-mix(in srgb, var(--accent-2) 84%, white), transparent);
            filter: blur(12px);
            opacity: 0;
            animation: quest-glow 1.7s ease 0.7s forwards;
        }

        .quest-result-copy {
            position: relative;
            z-index: 1;
            text-align: left;
        }

        .quest-result-eyebrow {
            margin: 0 0 12px;
            color: color-mix(in srgb, var(--accent-2) 82%, white);
            font-family: Arial, sans-serif;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 0.16em;
            text-transform: uppercase;
        }

        .quest-result-copy h1 {
            margin: 0;
            color: #ffffff;
            font-size: clamp(38px, 7vw, 82px);
            line-height: 0.92;
            text-shadow: 0 0 34px color-mix(in srgb, var(--accent) 42%, transparent);
        }

        .quest-result-rank {
            width: fit-content;
            margin: 16px 0 22px;
            padding: 7px 12px;
            border: 1px solid color-mix(in srgb, var(--accent-2) 42%, transparent);
            color: rgba(242, 238, 245, 0.74);
            background: rgba(255, 255, 255, 0.05);
            font-family: Arial, sans-serif;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .quest-result-message {
            max-width: 560px;
            color: rgba(242, 238, 245, 0.84);
            font-family: Arial, sans-serif;
            font-size: clamp(17px, 2vw, 22px);
            line-height: 1.55;
        }

        .quest-result--bonus .quest-safe-lid,
        .quest-result--bonus-strong .quest-safe-lid {
            animation-name: quest-lid-soft;
        }

        .quest-result--bonus .quest-result-visual::before,
        .quest-result--bonus-strong .quest-result-visual::before {
            opacity: 0.72;
            animation-duration: 4.2s;
        }

        .quest-result--closed .quest-safe-lid {
            animation: none;
        }

        .quest-result--closed .quest-safe-glow {
            display: none;
        }

        @keyframes quest-lid {
            0% {
                transform: rotateX(0deg) translateY(0);
            }

            100% {
                transform: rotateX(-58deg) translateY(-18px);
            }
        }

        @keyframes quest-lid-soft {
            0% {
                transform: rotateX(0deg) translateY(0);
            }

            100% {
                transform: rotateX(-24deg) translateY(-8px);
            }
        }

        @keyframes quest-glow {
            0% {
                opacity: 0;
                transform: translateY(8px) scaleX(0.8);
            }

            100% {
                opacity: 0.9;
                transform: translateY(0) scaleX(1);
            }
        }

        @keyframes quest-aura {
            0%,
            100% {
                transform: scale(0.96);
                opacity: 0.64;
            }

            50% {
                transform: scale(1.04);
                opacity: 0.92;
            }
        }

        @media (max-width: 760px) {
            .quest-result-card {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .quest-result-copy {
                text-align: center;
            }

            .quest-result-rank {
                margin-left: auto;
                margin-right: auto;
            }

            .quest-result-message {
                margin: 0 auto;
            }
        }

        @media (max-width: 480px) {
            .quest-result {
                padding: 14px;
            }

            .quest-result-card {
                padding: 28px 16px;
            }

            .quest-result-visual {
                min-height: 220px;
            }
        }
    </style>
@endsection
