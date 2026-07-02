@extends('layouts.app2')

@section('content')
    <style>
        body:has(.photo-awards-stage) {
            background: #0d0b08;
        }

        body:has(.photo-awards-stage) .dvanimation,
        body:has(.photo-awards-stage) [x-data="basic"] {
            min-height: 100svh;
            padding: 0 !important;
        }

        body:has(.photo-awards-stage) .fixed.bottom-6 {
            display: none !important;
        }

        .photo-awards-stage {
            --photo-bg: #0d0b08;
            --photo-panel: rgba(255, 250, 240, .075);
            --photo-line: rgba(244, 211, 107, .22);
            --photo-gold: #d4af37;
            --photo-gold-soft: #f4d36b;
            --photo-ink: #fffaf0;
            --photo-muted: rgba(255, 250, 240, .68);
            min-height: 100svh;
            background:
                linear-gradient(180deg, rgba(244, 211, 107, .1), transparent 30%),
                repeating-linear-gradient(90deg, rgba(255, 250, 240, .035) 0 1px, transparent 1px 92px),
                var(--photo-bg);
            color: var(--photo-ink);
            padding: clamp(14px, 2vw, 26px);
            user-select: none;
        }

        .photo-stage-frame {
            display: grid;
            gap: 18px;
            width: min(100%, 1540px);
            margin: 0 auto;
        }

        .photo-stage-header,
        .photo-stage-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            min-width: 0;
            border: 1px solid var(--photo-line);
            border-radius: 8px;
            background: rgba(0, 0, 0, .22);
            padding: clamp(14px, 2vw, 20px);
        }

        .photo-stage-title {
            min-width: 0;
        }

        .photo-kicker,
        .photo-stage-status {
            margin: 0;
            color: var(--photo-muted);
            font-weight: 900;
            letter-spacing: 0;
            text-transform: uppercase;
        }

        .photo-kicker {
            font-size: .78rem;
        }

        .photo-stage-title h1 {
            margin: 4px 0 0;
            overflow: hidden;
            color: #ffffff;
            font-size: clamp(1.8rem, 4vw, 3.5rem);
            font-weight: 900;
            line-height: 1.04;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .photo-count {
            display: grid;
            width: clamp(78px, 8vw, 112px);
            aspect-ratio: 1;
            place-items: center;
            border: 1px solid rgba(244, 211, 107, .48);
            border-radius: 999px;
            background: rgba(212, 175, 55, .14);
            color: var(--photo-gold-soft);
            font-size: clamp(1.5rem, 3vw, 2.6rem);
            font-weight: 900;
        }

        .photo-results-empty {
            display: grid;
            min-height: 50svh;
            place-items: center;
            border: 1px dashed rgba(244, 211, 107, .28);
            border-radius: 8px;
            background: var(--photo-panel);
            color: var(--photo-muted);
            font-size: 1.2rem;
            font-weight: 900;
            padding: 24px;
            text-align: center;
        }

        .photo-result-gallery {
            columns: 4 270px;
            column-gap: 18px;
        }

        .photo-result-card {
            position: relative;
            display: inline-block;
            width: 100%;
            margin: 0 0 18px;
            overflow: hidden;
            break-inside: avoid;
            border: 1px solid rgba(244, 211, 107, .2);
            border-radius: 8px;
            background: rgba(255, 250, 240, .08);
            box-shadow: 0 14px 36px rgba(0, 0, 0, .2);
            filter: saturate(.72);
            opacity: .58;
            transform: translateY(10px);
            transition: transform .55s ease, opacity .55s ease, filter .55s ease, box-shadow .55s ease, border-color .55s ease;
        }

        .photo-result-card img {
            display: block;
            width: 100%;
            height: auto;
            background: rgba(0, 0, 0, .28);
            object-fit: contain;
        }

        .photo-result-body {
            position: absolute;
            right: 10px;
            bottom: 10px;
            left: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            opacity: 0;
            transform: translateY(8px);
            transition: opacity .35s ease, transform .35s ease;
        }

        .score,
        .winner-label {
            display: inline-flex;
            min-height: 34px;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            font-weight: 900;
            padding: 0 12px;
        }

        .score {
            background: rgba(0, 0, 0, .72);
            color: var(--photo-gold-soft);
        }

        .winner-label {
            background: var(--photo-gold);
            color: #111111;
        }

        .photo-result-card.revealed {
            filter: saturate(1);
            opacity: 1;
            transform: translateY(0);
        }

        .photo-result-card.revealed .photo-result-body {
            opacity: 1;
            transform: translateY(0);
        }

        .photo-result-card.revealed.is-winner {
            border-color: rgba(244, 211, 107, .95);
            box-shadow: 0 22px 64px rgba(212, 175, 55, .28);
            transform: translateY(-4px);
        }

        .photo-stage-status {
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .photo-reveal-button {
            display: inline-flex;
            min-height: 46px;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 8px;
            background: var(--photo-gold);
            color: #111111;
            cursor: pointer;
            font: inherit;
            font-weight: 900;
            padding: 0 20px;
            white-space: nowrap;
            transition: background .2s ease, opacity .2s ease, transform .2s ease;
        }

        .photo-reveal-button:hover:not(:disabled) {
            background: var(--photo-gold-soft);
            transform: translateY(-1px);
        }

        .photo-reveal-button:disabled {
            cursor: default;
            opacity: .68;
        }

        @media (max-width: 760px) {
            .photo-stage-header,
            .photo-stage-footer {
                align-items: flex-start;
                flex-direction: column;
            }

            .photo-stage-title h1 {
                white-space: normal;
            }

            .photo-count {
                width: 68px;
            }

            .photo-stage-status,
            .photo-reveal-button {
                width: 100%;
            }
        }
    </style>

    <section id="photoAwardsStage" class="photo-awards-stage" aria-label="Результати фото-голосування">
        <div class="photo-stage-frame">
            <header class="photo-stage-header">
                <div class="photo-stage-title">
                    <p class="photo-kicker">Cherry Camp Awards</p>
                    <h1>{{ $vote->name }}</h1>
                </div>
                <div class="photo-count" aria-label="{{ $photos->count() }} фото">{{ $photos->count() }}</div>
            </header>

            @if($photos->isEmpty())
                <div class="photo-results-empty">
                    Фото ще не завантажено.
                </div>
            @else
                <div id="photoResults" class="photo-result-gallery">
                    @foreach($photos as $photo)
                        @php
                            $isWinner = $maxVotes > 0 && $photo->score === $maxVotes;
                        @endphp
                        <article class="photo-result-card {{ $isWinner ? 'is-winner' : '' }}" data-score="{{ $photo->score }}">
                            <img src="{{ asset($photo->image_path) }}" alt="Фото учасника">
                            <div class="photo-result-body">
                                <span class="score">{{ $photo->score }} балів</span>
                                @if($isWinner)
                                    <span class="winner-label">Переможець</span>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>

                <footer class="photo-stage-footer">
                    <p id="resultStatus" class="photo-stage-status">Результати запечатані</p>
                    <button id="revealPhotoResult" class="photo-reveal-button" type="button">Відкрити переможця</button>
                </footer>
            @endif
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const stage = document.getElementById('photoAwardsStage');
            const button = document.getElementById('revealPhotoResult');
            const status = document.getElementById('resultStatus');
            const cards = Array.from(document.querySelectorAll('.photo-result-card'));

            if (stage) {
                stage.addEventListener('contextmenu', (event) => event.preventDefault());
            }

            if (!button || cards.length === 0) {
                return;
            }

            button.addEventListener('click', () => {
                button.disabled = true;
                button.textContent = 'Відкриваємо...';

                if (status) {
                    status.textContent = 'Відкриваємо галерею';
                }

                cards.forEach((card, index) => {
                    window.setTimeout(() => {
                        card.classList.add('revealed');

                        if (index === cards.length - 1) {
                            if (status) {
                                status.textContent = 'Переможця відкрито';
                            }
                            document.querySelector('.photo-result-card.is-winner')?.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center',
                                inline: 'center',
                            });
                            button.textContent = 'Готово';
                        }
                    }, index * 180);
                });
            });
        });
    </script>
@endsection
