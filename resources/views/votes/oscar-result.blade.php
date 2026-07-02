@extends('layouts.app2')

@section('content')
    @php
        $categoryTitles = [
            'best_camera' => 'Оператор',
            'best_editing' => 'Монтаж',
            'best_actress' => 'Жіноча роль',
            'best_actor' => 'Чоловіча роль',
            'best_director' => 'Режисер',
        ];
    @endphp

    <style>
        body:has(.oscar-results-stage) {
            overflow: hidden;
            background: #0d0b08;
        }

        body:has(.oscar-results-stage) .dvanimation,
        body:has(.oscar-results-stage) [x-data="basic"] {
            min-height: 100svh;
            padding: 0 !important;
        }

        body:has(.oscar-results-stage) .fixed.bottom-6 {
            display: none !important;
        }

        .oscar-results-stage {
            --oscar-bg: #0d0b08;
            --oscar-panel: rgba(255, 250, 240, .075);
            --oscar-line: rgba(244, 211, 107, .22);
            --oscar-gold: #d4af37;
            --oscar-gold-soft: #f4d36b;
            --oscar-ink: #fffaf0;
            --oscar-muted: rgba(255, 250, 240, .68);
            display: grid;
            grid-template-rows: auto minmax(0, 1fr) auto;
            gap: 18px;
            width: 100%;
            height: 100svh;
            overflow: hidden;
            background:
                linear-gradient(180deg, rgba(244, 211, 107, .1), transparent 34%),
                repeating-linear-gradient(90deg, rgba(255, 250, 240, .035) 0 1px, transparent 1px 92px),
                var(--oscar-bg);
            color: var(--oscar-ink);
            padding: clamp(14px, 2vw, 24px);
            user-select: none;
        }

        .oscar-stage-header,
        .oscar-stage-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            min-width: 0;
        }

        .oscar-stage-title {
            min-width: 0;
        }

        .oscar-kicker,
        .oscar-stage-status,
        .award-overline {
            margin: 0;
            color: var(--oscar-muted);
            font-weight: 900;
            letter-spacing: 0;
            text-transform: uppercase;
        }

        .oscar-kicker,
        .award-overline {
            font-size: .78rem;
        }

        .oscar-stage-title h1 {
            margin: 4px 0 0;
            overflow: hidden;
            color: #ffffff;
            font-size: clamp(1.7rem, 3.2vw, 3rem);
            font-weight: 900;
            line-height: 1.04;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .oscar-progress {
            display: flex;
            flex: 0 0 auto;
            gap: 8px;
        }

        .oscar-progress-dot {
            width: 34px;
            height: 6px;
            border-radius: 999px;
            background: rgba(255, 250, 240, .2);
            transition: background .25s ease, width .25s ease;
        }

        .oscar-progress-dot.is-active {
            width: 54px;
            background: var(--oscar-gold);
        }

        .oscar-slides {
            position: relative;
            min-height: 0;
            overflow: hidden;
        }

        .award-slide {
            display: none;
            grid-template-rows: auto minmax(0, 1fr);
            gap: 16px;
            height: 100%;
            min-height: 0;
        }

        .award-slide.is-active {
            display: grid;
        }

        .award-slide-head {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 18px;
            min-width: 0;
            border: 1px solid var(--oscar-line);
            border-radius: 8px;
            background: rgba(0, 0, 0, .22);
            padding: clamp(14px, 2vw, 20px);
        }

        .award-slide-head h2 {
            margin: 4px 0 0;
            color: #ffffff;
            font-size: clamp(2.2rem, 6vw, 5rem);
            font-weight: 900;
            line-height: .95;
        }

        .award-seal {
            display: grid;
            width: clamp(76px, 8vw, 112px);
            aspect-ratio: 1;
            place-items: center;
            border: 1px solid rgba(244, 211, 107, .48);
            border-radius: 999px;
            background: rgba(212, 175, 55, .14);
            color: var(--oscar-gold-soft);
            font-size: clamp(1.6rem, 3vw, 2.6rem);
            font-weight: 900;
        }

        .award-slide-body {
            position: relative;
            display: grid;
            grid-template-rows: auto minmax(0, 1fr) auto;
            gap: 16px;
            min-height: 0;
            overflow: hidden;
            border: 1px solid var(--oscar-line);
            border-radius: 8px;
            background: var(--oscar-panel);
            padding: clamp(14px, 2vw, 22px);
        }

        .envelope-panel {
            display: grid;
            place-items: center;
            min-height: 112px;
            transition: opacity .45s ease, transform .45s ease;
        }

        .award-envelope {
            position: relative;
            width: min(360px, 72vw);
            aspect-ratio: 1.65 / 1;
            border: 1px solid rgba(244, 211, 107, .66);
            border-radius: 8px;
            background:
                linear-gradient(145deg, #f8e7aa, #c99f2c);
            box-shadow: 0 22px 60px rgba(0, 0, 0, .32), 0 0 38px rgba(212, 175, 55, .16);
            transform-origin: top center;
            transition: transform .65s ease, opacity .45s ease;
        }

        .award-envelope::before,
        .award-envelope::after {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: 8px;
        }

        .award-envelope::before {
            clip-path: polygon(0 0, 50% 54%, 100% 0);
            background: linear-gradient(180deg, #fff2bc, #d8ae35);
            transform-origin: top center;
            transition: transform .65s ease;
        }

        .award-envelope::after {
            clip-path: polygon(0 100%, 50% 45%, 100% 100%);
            background: linear-gradient(180deg, rgba(151, 104, 12, .08), rgba(117, 78, 8, .28));
        }

        .award-slide.is-opening .award-envelope::before,
        .award-slide.is-revealed .award-envelope::before {
            transform: rotateX(178deg);
        }

        .award-slide.is-revealed .envelope-panel {
            opacity: 0;
            pointer-events: none;
            transform: translateY(-14px);
        }

        .nominee-result-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
            gap: clamp(12px, 1.5vw, 18px);
            min-height: 0;
            overflow: auto;
            padding: 2px;
        }

        .award-nominee {
            position: relative;
            display: grid;
            grid-template-rows: minmax(0, 1fr) auto;
            min-height: 0;
            overflow: hidden;
            border: 1px solid rgba(244, 211, 107, .2);
            border-radius: 8px;
            background: rgba(255, 250, 240, .08);
            filter: saturate(.7);
            opacity: .46;
            transform: translateY(10px);
            transition: opacity .45s ease, filter .45s ease, transform .45s ease, border-color .45s ease, box-shadow .45s ease;
        }

        .award-nominee-image {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 220px;
            overflow: hidden;
            background: rgba(0, 0, 0, .28);
        }

        .award-nominee-image img {
            display: block;
            width: auto;
            max-width: 100%;
            height: auto;
            max-height: min(43vh, 430px);
            object-fit: contain;
        }

        .award-nominee-body {
            display: grid;
            gap: 7px;
            padding: 12px;
            text-align: center;
        }

        .award-nominee-body h3 {
            margin: 0;
            color: #ffffff;
            font-size: clamp(1rem, 1.35vw, 1.35rem);
            font-weight: 900;
            line-height: 1.12;
        }

        .award-score,
        .winner-badge {
            opacity: 0;
            transform: translateY(6px);
            transition: opacity .35s ease, transform .35s ease;
        }

        .award-score {
            margin: 0;
            color: var(--oscar-gold-soft);
            font-size: 1rem;
            font-weight: 900;
        }

        .winner-badge {
            display: inline-flex;
            min-height: 30px;
            align-items: center;
            justify-content: center;
            justify-self: center;
            border-radius: 999px;
            background: var(--oscar-gold);
            color: #111111;
            font-weight: 900;
            padding: 0 12px;
        }

        .award-slide.is-revealed .award-nominee {
            opacity: 1;
            filter: saturate(1);
            transform: translateY(0);
        }

        .award-slide.is-revealed .award-score,
        .award-slide.is-revealed .winner-badge {
            opacity: 1;
            transform: translateY(0);
        }

        .award-slide.is-revealed .award-nominee.is-winner {
            border-color: rgba(244, 211, 107, .9);
            box-shadow: 0 20px 56px rgba(212, 175, 55, .22);
            transform: translateY(-5px);
        }

        .empty-award {
            display: grid;
            min-height: 0;
            place-items: center;
            border: 1px dashed rgba(244, 211, 107, .28);
            border-radius: 8px;
            color: var(--oscar-muted);
            font-size: 1.2rem;
            font-weight: 900;
            padding: 24px;
            text-align: center;
        }

        .oscar-stage-status {
            min-width: 0;
            overflow: hidden;
            border: 1px solid rgba(255, 250, 240, .14);
            border-radius: 8px;
            background: rgba(0, 0, 0, .22);
            padding: 10px 12px;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .award-action-button {
            display: inline-flex;
            min-height: 46px;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 8px;
            background: var(--oscar-gold);
            color: #111111;
            cursor: pointer;
            font: inherit;
            font-weight: 900;
            padding: 0 20px;
            white-space: nowrap;
            transition: background .2s ease, opacity .2s ease, transform .2s ease;
        }

        .award-action-button:hover:not(:disabled) {
            background: var(--oscar-gold-soft);
            transform: translateY(-1px);
        }

        .award-action-button:disabled {
            cursor: default;
            opacity: .68;
        }

        @media (max-width: 760px) {
            .oscar-results-stage {
                gap: 12px;
                overflow: auto;
            }

            .oscar-stage-header,
            .oscar-stage-footer,
            .award-slide-head {
                align-items: flex-start;
                flex-direction: column;
            }

            .oscar-stage-title h1 {
                white-space: normal;
            }

            .oscar-progress-dot {
                width: 26px;
            }

            .oscar-progress-dot.is-active {
                width: 40px;
            }

            .award-slide-head h2 {
                font-size: clamp(2.1rem, 13vw, 4.2rem);
            }

            .award-seal {
                width: 68px;
            }

            .nominee-result-gallery {
                grid-template-columns: 1fr;
                overflow: visible;
            }

            .oscar-stage-status,
            .award-action-button {
                width: 100%;
            }
        }

        @media (max-height: 680px) and (min-width: 761px) {
            .oscar-results-stage {
                gap: 10px;
                padding: 10px;
            }

            .award-slide-head h2 {
                font-size: clamp(1.8rem, 4.8vw, 3.6rem);
            }

            .envelope-panel {
                min-height: 74px;
            }

            .award-envelope {
                width: min(260px, 48vw);
            }

            .award-nominee-image {
                min-height: 160px;
            }

            .award-nominee-image img {
                max-height: min(34vh, 300px);
            }
        }
    </style>

    <section id="oscarResultsStage" class="oscar-results-stage" aria-label="Результати Оскару">
        <header class="oscar-stage-header">
            <div class="oscar-stage-title">
                <p class="oscar-kicker">Cherry Camp Awards</p>
                <h1>{{ $vote->name }}</h1>
            </div>
            <div class="oscar-progress" aria-hidden="true">
                @foreach($results as $result)
                    <span class="oscar-progress-dot {{ $loop->first ? 'is-active' : '' }}"></span>
                @endforeach
            </div>
        </header>

        @if($results->isEmpty())
            <div class="empty-award">Для цього голосування ще немає номінацій.</div>
        @else
            <div class="oscar-slides">
                @foreach($results as $key => $result)
                    @php
                        $shortTitle = $categoryTitles[$key] ?? $result['title'];
                        $hasNominees = $result['nominees']->isNotEmpty();
                    @endphp

                    <section
                        class="award-slide {{ $loop->first ? 'is-active' : '' }}"
                        data-title="{{ $shortTitle }}"
                        data-has-nominees="{{ $hasNominees ? 'true' : 'false' }}"
                    >
                        <div class="award-slide-head">
                            <div>
                                <p class="award-overline">Номінація {{ $loop->iteration }} / {{ $results->count() }}</p>
                                <h2>{{ $shortTitle }}</h2>
                            </div>
                            <div class="award-seal" aria-hidden="true">{{ $loop->iteration }}</div>
                        </div>

                        <div class="award-slide-body">
                            @if($hasNominees)
                                <div class="envelope-panel" aria-hidden="true">
                                    <div class="award-envelope"></div>
                                </div>

                                <div class="nominee-result-gallery">
                                    @foreach($result['nominees'] as $nominee)
                                        @php
                                            $isWinner = $result['maxScore'] > 0 && $nominee->oscar_score === $result['maxScore'];
                                        @endphp
                                        <article class="award-nominee {{ $isWinner ? 'is-winner' : '' }}">
                                            <div class="award-nominee-image">
                                                <img src="{{ $nominee->image_url }}" alt="{{ $nominee->name }}">
                                            </div>
                                            <div class="award-nominee-body">
                                                <h3>{{ $nominee->name }}</h3>
                                                <p class="award-score">{{ $nominee->oscar_score }} голосів</p>
                                                @if($isWinner)
                                                    <span class="winner-badge">Переможець</span>
                                                @endif
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-award">У цій номінації ще немає голосів.</div>
                            @endif
                        </div>
                    </section>
                @endforeach
            </div>

            <footer class="oscar-stage-footer">
                <p id="awardStageStatus" class="oscar-stage-status">Оператор: конверт запечатаний</p>
                <button id="awardActionButton" type="button" class="award-action-button">Відкрити конверт</button>
            </footer>
        @endif
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const stage = document.getElementById('oscarResultsStage');
            const slides = Array.from(document.querySelectorAll('.award-slide'));
            const dots = Array.from(document.querySelectorAll('.oscar-progress-dot'));
            const actionButton = document.getElementById('awardActionButton');
            const status = document.getElementById('awardStageStatus');
            let currentIndex = 0;

            if (stage) {
                stage.addEventListener('contextmenu', (event) => event.preventDefault());
            }

            if (!actionButton || slides.length === 0) {
                return;
            }

            const currentSlide = () => slides[currentIndex];

            const setStatus = (message) => {
                if (status) {
                    status.textContent = message;
                }
            };

            const updateControls = () => {
                const slide = currentSlide();
                const title = slide.dataset.title;
                const hasNominees = slide.dataset.hasNominees === 'true';
                const isRevealed = slide.classList.contains('is-revealed');
                const isLast = currentIndex === slides.length - 1;

                dots.forEach((dot, index) => {
                    dot.classList.toggle('is-active', index === currentIndex);
                });

                if (!hasNominees) {
                    setStatus(title + ': немає голосів');
                    actionButton.textContent = isLast ? 'Завершити' : 'Наступна номінація';
                    actionButton.disabled = false;
                    return;
                }

                if (isRevealed) {
                    setStatus(title + ': переможця оголошено');
                    actionButton.textContent = isLast ? 'Завершити' : 'Наступна номінація';
                    actionButton.disabled = false;
                    return;
                }

                setStatus(title + ': конверт запечатаний');
                actionButton.textContent = 'Відкрити конверт';
                actionButton.disabled = false;
            };

            const showSlide = (index) => {
                currentIndex = index;
                slides.forEach((slide, slideIndex) => {
                    slide.classList.toggle('is-active', slideIndex === currentIndex);
                });
                updateControls();
            };

            actionButton.addEventListener('click', () => {
                const slide = currentSlide();
                const title = slide.dataset.title;
                const hasNominees = slide.dataset.hasNominees === 'true';
                const isRevealed = slide.classList.contains('is-revealed');
                const isLast = currentIndex === slides.length - 1;

                if (!hasNominees || isRevealed) {
                    if (isLast) {
                        setStatus('Церемонію завершено');
                        actionButton.textContent = 'Готово';
                        actionButton.disabled = true;
                        return;
                    }

                    showSlide(currentIndex + 1);
                    return;
                }

                actionButton.disabled = true;
                actionButton.textContent = 'Відкриваємо...';
                setStatus(title + ': відкриваємо конверт');
                slide.classList.add('is-opening');

                window.setTimeout(() => {
                    slide.classList.remove('is-opening');
                    slide.classList.add('is-revealed');
                    slide.querySelector('.award-nominee.is-winner')?.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center',
                        inline: 'center',
                    });
                    updateControls();
                }, 1250);
            });

            updateControls();
        });
    </script>
@endsection
