@extends('layouts.users.app')

@section('content')
    <section id="voteResultScreen" class="vote-results-screen" style="--team-count: {{ max($teams->count(), 1) }}">
        <div class="result-frame">
            <header class="result-topbar">
                <div class="result-title">
                    <p>Командне голосування</p>
                    <h1>{{ $vote->name }}</h1>
                </div>

                <div class="result-actions">
                    <p id="resultStatus" class="result-status">Результати запечатані</p>
                    <button id="fullscreenBtn" class="result-icon-button" type="button" aria-label="На весь екран" title="На весь екран">
                        <svg class="icon-enter" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M4 9V4h5M20 9V4h-5M4 15v5h5M20 15v5h-5" />
                        </svg>
                        <svg class="icon-exit" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M9 4v5H4M15 4v5h5M9 20v-5H4M15 20v-5h5" />
                        </svg>
                    </button>
                </div>
            </header>

            @if($teams->isEmpty())
                <div class="empty-results">
                    <p>Ще немає голосів.</p>
                </div>
            @else
                <div id="teamResults" class="team-results" data-max="{{ $maxVotes }}">
                    @foreach($teams as $team)
                        @php
                            $scoreSize = $maxVotes > 0 ? max(7, ($team['count'] / $maxVotes) * 100) : 7;
                            $fallback = mb_substr($team['element_name'], 0, 1);
                        @endphp

                        <article
                            class="team-result-card {{ $team['is_winner'] ? 'is-winner' : '' }}"
                            style="--team-color: {{ $team['color'] }}; --score-size: {{ $scoreSize }}%;"
                            data-score="{{ $team['count'] }}"
                        >
                            <div class="score-column" aria-hidden="true">
                                <div class="score-rail">
                                    <div class="score-fill"></div>
                                </div>
                            </div>

                            <div class="team-meta">
                                <div class="team-mark" data-fallback="{{ $fallback }}">
                                    <img
                                        src="{{ asset($team['logo']) }}"
                                        alt="{{ $team['element_name'] }}"
                                        onerror="this.parentElement.classList.add('mark-fallback'); this.remove();"
                                    >
                                </div>

                                <div class="team-copy">
                                    <p>{{ $team['element_name'] }}</p>
                                    <h2>{{ $team['name'] }}</h2>
                                </div>

                                <div class="score-number" aria-label="{{ $team['count'] }} балів">
                                    <span class="score-secret">?</span>
                                    <span class="score-open">{{ $team['count'] }}</span>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="result-footer">
                    <button id="revealBtn" class="reveal-results-button" type="button">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 5v14M5 12h14" />
                        </svg>
                        <span>Відкрити результати</span>
                    </button>
                </div>
            @endif
        </div>
    </section>
@endsection

@section('styles')
    <style>
        .vote-results-screen {
            --stage-bg: #111111;
            --stage-ink: #fffaf0;
            --muted-ink: rgba(255, 250, 240, .72);
            height: calc(100svh - 128px);
            min-height: 0;
            overflow: hidden;
        }

        .result-frame {
            position: relative;
            isolation: isolate;
            display: grid;
            grid-template-rows: auto minmax(0, 1fr) auto;
            gap: 16px;
            height: 100%;
            overflow: hidden;
            border: 1px solid rgba(255, 250, 240, .18);
            border-radius: 8px;
            background:
                linear-gradient(135deg, rgba(255, 255, 255, .12), rgba(255, 255, 255, 0) 36%),
                repeating-linear-gradient(90deg, rgba(255, 255, 255, .035) 0 1px, transparent 1px 72px),
                var(--stage-bg);
            padding: 22px;
            color: var(--stage-ink);
            box-shadow: 0 24px 54px rgba(17, 17, 17, .26);
        }

        .result-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            min-width: 0;
        }

        .result-title {
            min-width: 0;
        }

        .result-title p,
        .team-copy p,
        .result-status {
            margin: 0;
            color: var(--muted-ink);
            font-weight: 800;
            letter-spacing: 0;
        }

        .result-title p,
        .team-copy p {
            font-size: .82rem;
        }

        .result-title h1 {
            margin: 4px 0 0;
            overflow: hidden;
            color: #ffffff;
            font-size: 2.15rem;
            font-weight: 900;
            line-height: 1.08;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .result-actions {
            display: flex;
            flex: 0 0 auto;
            align-items: center;
            gap: 10px;
        }

        .result-status {
            border: 1px solid rgba(255, 250, 240, .16);
            border-radius: 8px;
            background: rgba(255, 250, 240, .08);
            padding: 9px 12px;
            font-size: .9rem;
            white-space: nowrap;
        }

        .result-icon-button,
        .reveal-results-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 8px;
            cursor: pointer;
            transition: transform .2s ease, background .2s ease, color .2s ease, opacity .2s ease;
        }

        .result-icon-button {
            width: 44px;
            height: 44px;
            background: #fffaf0;
            color: #111111;
        }

        .result-icon-button:hover,
        .reveal-results-button:hover {
            transform: translateY(-1px);
        }

        .result-icon-button svg,
        .reveal-results-button svg {
            width: 22px;
            height: 22px;
            fill: none;
            stroke: currentColor;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-width: 2;
        }

        .result-icon-button .icon-exit,
        .result-icon-button.is-active .icon-enter {
            display: none;
        }

        .result-icon-button.is-active .icon-exit {
            display: block;
        }

        .team-results {
            display: grid;
            grid-template-columns: repeat(var(--team-count), minmax(0, 1fr));
            gap: 16px;
            min-height: 0;
            overflow: hidden;
        }

        .team-result-card {
            --card-glow: color-mix(in srgb, var(--team-color) 34%, transparent);
            position: relative;
            display: grid;
            grid-template-rows: minmax(0, 1fr) auto;
            gap: 12px;
            min-width: 0;
            min-height: 0;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, .16);
            border-radius: 8px;
            background:
                linear-gradient(180deg, color-mix(in srgb, var(--team-color) 18%, transparent), rgba(255, 255, 255, .055)),
                rgba(255, 255, 255, .07);
            padding: 14px;
            filter: saturate(.56);
            opacity: .82;
            transition: border-color .5s ease, box-shadow .5s ease, filter .5s ease, opacity .5s ease, transform .5s ease;
        }

        .team-result-card.is-visible {
            filter: saturate(1);
            opacity: 1;
        }

        .team-result-card.is-final.is-winner {
            border-color: color-mix(in srgb, var(--team-color) 78%, #fffaf0);
            box-shadow: 0 0 0 1px color-mix(in srgb, var(--team-color) 45%, transparent), 0 22px 46px var(--card-glow);
            transform: translateY(-4px);
        }

        .score-column {
            display: flex;
            align-items: flex-end;
            justify-content: center;
            min-height: 0;
            overflow: hidden;
        }

        .score-rail {
            position: relative;
            width: min(74px, 72%);
            height: 100%;
            min-height: 0;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, .16);
            border-radius: 8px;
            background: rgba(0, 0, 0, .24);
        }

        .score-rail::before {
            content: "";
            position: absolute;
            inset: 0;
            background: repeating-linear-gradient(180deg, transparent 0 18px, rgba(255, 255, 255, .08) 18px 19px);
        }

        .score-fill {
            position: absolute;
            right: 0;
            bottom: 0;
            left: 0;
            height: 0;
            border-radius: 8px 8px 0 0;
            background:
                linear-gradient(180deg, color-mix(in srgb, var(--team-color) 62%, #ffffff), var(--team-color)),
                var(--team-color);
            box-shadow: 0 0 28px color-mix(in srgb, var(--team-color) 52%, transparent);
            transition: height 1.05s cubic-bezier(.2, .8, .2, 1);
        }

        .team-result-card.is-visible .score-fill {
            height: var(--score-size);
        }

        .team-meta {
            display: grid;
            grid-template-columns: clamp(48px, 4.2vw, 64px) minmax(0, 1fr) clamp(48px, 4.2vw, 58px);
            align-items: center;
            gap: clamp(8px, .9vw, 12px);
            min-width: 0;
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 8px;
            background: rgba(0, 0, 0, .22);
            padding: 10px;
        }

        .team-mark {
            position: relative;
            display: grid;
            width: 100%;
            aspect-ratio: 1;
            place-items: center;
            overflow: hidden;
            border-radius: 8px;
            background: rgba(255, 250, 240, .92);
        }

        .team-mark img {
            display: block;
            width: 88%;
            height: 88%;
            object-fit: contain;
        }

        .team-mark.mark-fallback::after {
            content: attr(data-fallback);
            color: #111111;
            font-size: 1.7rem;
            font-weight: 900;
        }

        .team-copy {
            min-width: 0;
            text-align: left;
        }

        .team-copy p {
            display: none;
        }

        .team-copy h2 {
            margin: 0;
            overflow: hidden;
            color: #ffffff;
            font-size: clamp(1rem, 1.18vw, 1.28rem);
            font-weight: 900;
            line-height: 1.05;
            text-overflow: ellipsis;
            white-space: nowrap;
            word-break: keep-all;
        }

        .score-number {
            position: relative;
            display: grid;
            width: 100%;
            aspect-ratio: 1;
            place-items: center;
            flex: 0 0 auto;
            border-radius: 8px;
            background: #fffaf0;
            color: #111111;
            font-size: clamp(1.35rem, 2vw, 2rem);
            font-weight: 900;
            line-height: 1;
        }

        .score-open {
            position: absolute;
            opacity: 0;
            transform: translateY(8px) scale(.9);
            transition: opacity .35s ease, transform .35s ease;
        }

        .team-result-card.is-visible .score-secret {
            opacity: 0;
            transform: translateY(-8px) scale(.9);
        }

        .score-secret {
            transition: opacity .25s ease, transform .25s ease;
        }

        .team-result-card.is-visible .score-open {
            opacity: 1;
            transform: translateY(0) scale(1);
        }

        .result-footer {
            display: flex;
            justify-content: center;
            min-height: 44px;
        }

        .reveal-results-button {
            gap: 10px;
            min-height: 44px;
            background: #fffaf0;
            padding: 0 18px;
            color: #111111;
            font-weight: 900;
        }

        .reveal-results-button:disabled {
            cursor: default;
            opacity: .7;
            transform: none;
        }

        .empty-results {
            display: grid;
            min-height: 0;
            place-items: center;
            border: 1px solid rgba(255, 250, 240, .16);
            border-radius: 8px;
            background: rgba(255, 250, 240, .08);
            color: var(--stage-ink);
            font-size: 1.15rem;
            font-weight: 900;
        }

        body.vote-result-fullscreen {
            overflow: hidden !important;
        }

        body.vote-result-fullscreen .sidebar,
        body.vote-result-fullscreen header,
        body.vote-result-fullscreen .screen_loader,
        body.vote-result-fullscreen .fixed.bottom-6 {
            display: none !important;
        }

        body.vote-result-fullscreen .main-container,
        body.vote-result-fullscreen .main-content,
        body.vote-result-fullscreen .dvanimation,
        body.vote-result-fullscreen [x-data="basic"] {
            width: 100% !important;
            max-width: none !important;
            min-height: 100svh !important;
            margin: 0 !important;
            padding: 0 !important;
            transform: none !important;
        }

        body.vote-result-fullscreen .vote-results-screen {
            width: 100vw;
            height: 100svh;
        }

        body.vote-result-fullscreen .result-frame {
            border: 0;
            border-radius: 0;
        }

        .vote-results-screen:fullscreen {
            width: 100vw;
            height: 100vh;
            background: var(--stage-bg);
        }

        .vote-results-screen:fullscreen .result-frame {
            height: 100vh;
            border: 0;
            border-radius: 0;
        }

        .vote-results-screen:-webkit-full-screen {
            width: 100vw;
            height: 100vh;
            background: var(--stage-bg);
        }

        .vote-results-screen:-webkit-full-screen .result-frame {
            height: 100vh;
            border: 0;
            border-radius: 0;
        }

        @media (max-width: 980px) {
            .vote-results-screen {
                height: calc(100svh - 112px);
            }

            .result-frame {
                gap: 12px;
                padding: 16px;
            }

            .result-title h1 {
                font-size: 1.75rem;
            }

            .team-meta {
                grid-template-columns: minmax(0, 1fr);
                justify-items: center;
                text-align: center;
            }

            .team-mark {
                width: 52px;
            }

            .team-copy {
                text-align: center;
            }

            .score-number {
                width: 52px;
                font-size: 1.75rem;
            }
        }

        @media (max-width: 760px), (max-aspect-ratio: 4 / 5) {
            .result-topbar {
                align-items: flex-start;
            }

            .result-title h1 {
                white-space: normal;
            }

            .result-status {
                display: none;
            }

            .team-results {
                grid-template-columns: 1fr;
                grid-auto-rows: minmax(0, 1fr);
                gap: 10px;
            }

            .team-result-card {
                grid-template-columns: minmax(0, 1fr);
                grid-template-rows: auto minmax(0, 1fr);
                gap: 9px;
                padding: 10px;
            }

            .score-column {
                align-items: center;
                order: 2;
            }

            .score-rail {
                width: 100%;
                height: 18px;
            }

            .score-rail::before {
                background: repeating-linear-gradient(90deg, transparent 0 28px, rgba(255, 255, 255, .12) 28px 29px);
            }

            .score-fill {
                top: 0;
                right: auto;
                width: 0;
                height: 100%;
                border-radius: 8px;
                transition-property: width;
            }

            .team-result-card.is-visible .score-fill {
                width: var(--score-size);
                height: 100%;
            }

            .team-meta {
                grid-template-columns: 48px minmax(0, 1fr) 46px;
                justify-items: stretch;
                padding: 8px;
                text-align: left;
            }

            .team-mark {
                width: 100%;
            }

            .team-copy h2 {
                font-size: 1.1rem;
            }

            .score-number {
                width: 100%;
                font-size: 1.45rem;
            }
        }

        @media (max-height: 620px) {
            .vote-results-screen {
                height: calc(100svh - 94px);
            }

            .result-frame {
                gap: 8px;
                padding: 10px;
            }

            .result-title h1 {
                font-size: 1.45rem;
            }

            .result-title p,
            .team-copy p,
            .result-status {
                font-size: .76rem;
            }

            .result-icon-button,
            .reveal-results-button,
            .result-footer {
                min-height: 38px;
            }

            .result-icon-button {
                width: 38px;
                height: 38px;
            }

            .team-result-card {
                gap: 8px;
                padding: 9px;
            }

            .team-meta {
                gap: 8px;
                padding: 7px;
            }

            .team-mark {
                width: 44px;
            }

            .team-copy h2 {
                font-size: 1.05rem;
            }

            .score-number {
                width: 42px;
                font-size: 1.35rem;
            }
        }
    </style>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const revealButton = document.getElementById('revealBtn');
            const fullscreenButton = document.getElementById('fullscreenBtn');
            const fullscreenTarget = document.getElementById('voteResultScreen');
            const status = document.getElementById('resultStatus');
            const cards = Array.from(document.querySelectorAll('.team-result-card'));

            const setStatus = (message) => {
                if (status) {
                    status.textContent = message;
                }
            };

            if (revealButton && cards.length > 0) {
                revealButton.addEventListener('click', () => {
                    revealButton.disabled = true;
                    revealButton.querySelector('span').textContent = 'Відкриваємо...';
                    setStatus('Рахунок відкривається поступово');

                    cards.forEach((card, index) => {
                        window.setTimeout(() => {
                            card.classList.add('is-visible');
                        }, index * 900);
                    });

                    window.setTimeout(() => {
                        cards.forEach((card) => {
                            if (card.classList.contains('is-winner')) {
                                card.classList.add('is-final');
                            }
                        });

                        setStatus('Переможця відкрито');
                        revealButton.remove();
                    }, cards.length * 900 + 650);
                });
            }

            let fallbackFullscreen = false;

            const syncFullscreenState = () => {
                const isFullscreen = document.fullscreenElement === fullscreenTarget || fallbackFullscreen;

                document.body.classList.toggle('vote-result-fullscreen', isFullscreen);

                if (fullscreenButton) {
                    fullscreenButton.classList.toggle('is-active', isFullscreen);
                    fullscreenButton.setAttribute('aria-label', isFullscreen ? 'Вийти з повного екрана' : 'На весь екран');
                    fullscreenButton.setAttribute('title', isFullscreen ? 'Вийти з повного екрана' : 'На весь екран');
                }
            };

            if (fullscreenButton && fullscreenTarget) {
                fullscreenButton.addEventListener('click', async () => {
                    try {
                        if (!document.fullscreenElement && fullscreenTarget.requestFullscreen) {
                            await fullscreenTarget.requestFullscreen();
                            fallbackFullscreen = false;
                        } else if (document.fullscreenElement && document.exitFullscreen) {
                            await document.exitFullscreen();
                            fallbackFullscreen = false;
                        } else {
                            fallbackFullscreen = !fallbackFullscreen;
                        }
                    } catch (error) {
                        fallbackFullscreen = !fallbackFullscreen;
                    }

                    syncFullscreenState();
                });

                document.addEventListener('fullscreenchange', () => {
                    if (document.fullscreenElement !== fullscreenTarget) {
                        fallbackFullscreen = false;
                    }

                    syncFullscreenState();
                });
                syncFullscreenState();
            }
        });
    </script>
@endsection
