@extends('layouts.app2')

@section('content')
    <main class="zootopia-result zootopia-result--{{ $result['variant'] }}">
        <section class="zootopia-result-card">
            <div class="zootopia-result-visual" aria-hidden="true">
                <div class="zootopia-crate">
                    <div class="zootopia-crate-lid"></div>
                    <div class="zootopia-crate-body">
                        <span>ZPD</span>
                    </div>
                    <div class="zootopia-crate-glow"></div>
                </div>
            </div>

            <div class="zootopia-result-copy">
                <p class="zootopia-result-eyebrow">{{ $result['eyebrow'] }}</p>
                <h1>{{ $result['label'] }}</h1>
                <p class="zootopia-result-rank">{{ $result['rank'] }}</p>
                <div class="zootopia-result-message">
                    {{ $message }}
                </div>
            </div>
        </section>
    </main>

    <style>
        @media (min-width: 1024px) {
            body:has(.zootopia-result) .main-container .main-content {
                margin-left: 0 !important;
                margin-right: 0 !important;
            }
        }

        .zootopia-result {
            --accent: #e64b43;
            --accent-2: #ffc044;
            --box: #d84b45;
            --box-dark: #741d28;
            --paper: #eefaff;
            min-height: calc(100vh - 48px);
            margin: -1.5rem;
            display: grid;
            place-items: center;
            padding: clamp(18px, 4vw, 64px);
            color: var(--paper);
            background:
                linear-gradient(180deg, rgba(7, 23, 64, 0.48), rgba(7, 23, 64, 0.95)),
                url("{{ asset('assets/images/zootopia-police-server.webp') }}") center / cover no-repeat;
            font-family: Nunito, Arial, sans-serif;
            overflow: hidden;
        }

        .zootopia-result--silver {
            --accent: #9aa8b6;
            --accent-2: #f3f7fb;
            --box: #b9c3cf;
            --box-dark: #536071;
        }

        .zootopia-result--black {
            --accent: #1c2433;
            --accent-2: #7dd9f5;
            --box: #1b2332;
            --box-dark: #070b13;
        }

        .zootopia-result--bonus,
        .zootopia-result--bonus-strong {
            --accent: #18c0ca;
            --accent-2: #ffc044;
            --box: #0b6e98;
            --box-dark: #07375d;
        }

        .zootopia-result--closed {
            --accent: #42506b;
            --accent-2: #93a4ba;
            --box: #2f3b52;
            --box-dark: #111827;
        }

        .zootopia-result-card {
            position: relative;
            width: min(100%, 1020px);
            display: grid;
            grid-template-columns: minmax(220px, 0.9fr) minmax(280px, 1.1fr);
            gap: clamp(24px, 5vw, 56px);
            align-items: center;
            border: 1px solid rgba(157, 226, 255, 0.26);
            border-radius: 30px;
            background:
                linear-gradient(135deg, rgba(24, 192, 202, 0.2), rgba(93, 69, 176, 0.26)),
                rgba(8, 21, 58, 0.92);
            box-shadow: 0 34px 90px rgba(4, 12, 35, 0.62);
            padding: clamp(24px, 5vw, 58px);
            overflow: hidden;
        }

        .zootopia-result-card::before {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background:
                radial-gradient(circle at 20% 18%, color-mix(in srgb, var(--accent-2) 28%, transparent), transparent 28%),
                radial-gradient(circle at 80% 82%, color-mix(in srgb, var(--accent) 30%, transparent), transparent 28%);
        }

        .zootopia-result-card > * {
            position: relative;
            z-index: 1;
        }

        .zootopia-result-visual {
            min-height: 300px;
            display: grid;
            place-items: center;
        }

        .zootopia-crate {
            position: relative;
            width: min(68vw, 280px);
            aspect-ratio: 1.08;
            filter: drop-shadow(0 28px 34px rgba(0, 0, 0, 0.48));
        }

        .zootopia-crate-lid {
            position: absolute;
            left: 8%;
            right: 8%;
            top: 6%;
            height: 22%;
            border: 1px solid rgba(255, 255, 255, 0.24);
            border-bottom: 0;
            border-radius: 16px 16px 0 0;
            background:
                linear-gradient(135deg, rgba(255, 255, 255, 0.2), transparent 38%),
                linear-gradient(180deg, var(--box), var(--box-dark));
            transform-origin: bottom center;
            animation: zootopiaLid 1.9s cubic-bezier(0.18, 1.2, 0.22, 1) forwards;
        }

        .zootopia-crate-body {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 76%;
            display: grid;
            place-items: center;
            border: 1px solid rgba(255, 255, 255, 0.24);
            border-radius: 18px;
            background:
                linear-gradient(135deg, rgba(255, 255, 255, 0.2), transparent 36%),
                linear-gradient(180deg, var(--box), var(--box-dark));
            box-shadow:
                inset 0 0 0 10px rgba(0, 0, 0, 0.14),
                inset 0 -40px 70px rgba(0, 0, 0, 0.24);
        }

        .zootopia-crate-body span {
            display: grid;
            place-items: center;
            width: 86px;
            height: 86px;
            border-radius: 24px;
            border: 8px solid color-mix(in srgb, var(--accent-2) 78%, #ffffff);
            color: #ffffff;
            background: rgba(3, 12, 37, 0.44);
            font-size: 22px;
            font-weight: 1000;
            box-shadow: 0 0 24px color-mix(in srgb, var(--accent-2) 32%, transparent);
        }

        .zootopia-crate-glow {
            position: absolute;
            left: 12%;
            right: 12%;
            top: 26%;
            height: 22%;
            background: linear-gradient(90deg, transparent, color-mix(in srgb, var(--accent-2) 88%, white), transparent);
            filter: blur(12px);
            opacity: 0;
            animation: zootopiaGlow 1.7s ease 0.7s forwards;
        }

        .zootopia-result-copy {
            text-align: left;
        }

        .zootopia-result-eyebrow {
            margin: 0 0 12px;
            color: color-mix(in srgb, var(--accent-2) 84%, white);
            font-size: 12px;
            font-weight: 1000;
            letter-spacing: 0.14em;
            text-transform: uppercase;
        }

        .zootopia-result-copy h1 {
            margin: 0;
            color: #ffffff;
            font-size: clamp(40px, 7vw, 84px);
            font-weight: 1000;
            line-height: 0.92;
            text-shadow: 0 10px 28px rgba(0, 0, 0, 0.34);
        }

        .zootopia-result-rank {
            width: fit-content;
            margin: 16px 0 22px;
            border: 1px solid color-mix(in srgb, var(--accent-2) 42%, transparent);
            border-radius: 14px;
            color: rgba(238, 250, 255, 0.82);
            background: rgba(255, 255, 255, 0.07);
            font-size: 12px;
            font-weight: 1000;
            letter-spacing: 0.08em;
            padding: 8px 12px;
            text-transform: uppercase;
        }

        .zootopia-result-message {
            max-width: 560px;
            color: rgba(238, 250, 255, 0.88);
            font-size: clamp(17px, 2vw, 22px);
            font-weight: 800;
            line-height: 1.55;
        }

        .zootopia-result--bonus .zootopia-crate-lid,
        .zootopia-result--bonus-strong .zootopia-crate-lid {
            animation-name: zootopiaLidSoft;
        }

        .zootopia-result--closed .zootopia-crate-lid {
            animation: none;
        }

        .zootopia-result--closed .zootopia-crate-glow {
            display: none;
        }

        @keyframes zootopiaLid {
            to {
                transform: rotateX(-58deg) translateY(-18px);
            }
        }

        @keyframes zootopiaLidSoft {
            to {
                transform: rotateX(-24deg) translateY(-8px);
            }
        }

        @keyframes zootopiaGlow {
            to {
                opacity: 0.9;
                transform: translateY(0) scaleX(1);
            }
        }

        @media (max-width: 760px) {
            .zootopia-result-card {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .zootopia-result-copy {
                text-align: center;
            }

            .zootopia-result-rank {
                margin-left: auto;
                margin-right: auto;
            }

            .zootopia-result-message {
                margin: 0 auto;
            }
        }
    </style>
@endsection
