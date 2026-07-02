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

        body:has(.oscar-results-stage) .main-container,
        body:has(.oscar-results-stage) .main-container .main-content {
            width: 100vw !important;
            max-width: none !important;
            min-width: 0 !important;
            margin-right: 0 !important;
            margin-left: 0 !important;
            padding-right: 0 !important;
            padding-left: 0 !important;
        }

        body:has(.oscar-results-stage) .dvanimation,
        body:has(.oscar-results-stage) [x-data="basic"] {
            width: 100vw;
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
            position: fixed;
            inset: 0;
            z-index: 70;
            display: grid;
            grid-template-rows: auto minmax(0, 1fr) auto;
            gap: 18px;
            width: auto;
            height: auto;
            min-width: 0;
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
            grid-template-rows: minmax(0, 1fr);
            min-height: 0;
            overflow: hidden;
            border: 1px solid var(--oscar-line);
            border-radius: 8px;
            background: var(--oscar-panel);
            padding: clamp(12px, 1.6vw, 20px);
        }

        .envelope-panel {
            position: fixed;
            inset: 0;
            z-index: 300;
            display: grid;
            place-items: center;
            background:
                radial-gradient(circle at center, rgba(212, 175, 55, .24), transparent 44%),
                rgba(13, 11, 8, .72);
            backdrop-filter: blur(4px);
            opacity: 0;
            overflow: hidden;
            perspective: 1200px;
            pointer-events: none;
            transform: scale(.98);
            transition: opacity .45s ease, transform .45s ease, visibility .45s ease;
            visibility: hidden;
        }

        .award-slide.is-opening .envelope-panel {
            opacity: 1;
            transform: scale(1);
            visibility: visible;
        }

        .award-envelope-scene {
            position: relative;
            z-index: 4;
            width: min(460px, 74vw);
            aspect-ratio: 1.65 / 1;
            filter: drop-shadow(0 26px 42px rgba(0, 0, 0, .38));
            transform: translateY(10px) scale(.94);
            transition: transform .4s ease;
        }

        .award-slide.is-opening .award-envelope-scene {
            animation: envelopeSceneArrive 5.2s cubic-bezier(.16, 1, .3, 1) forwards;
        }

        .award-envelope {
            position: relative;
            width: 100%;
            height: 100%;
            border: 1px solid rgba(244, 211, 107, .66);
            border-radius: 8px;
            background:
                linear-gradient(145deg, #f8e7aa, #c99f2c);
            box-shadow: 0 0 42px rgba(212, 175, 55, .2);
            transform-origin: top center;
            transform-style: preserve-3d;
            transition: transform .65s ease, opacity .45s ease;
        }

        .award-letter {
            position: absolute;
            bottom: 10%;
            left: 50%;
            z-index: 1;
            display: grid;
            grid-template-rows: minmax(0, 1fr) auto;
            width: clamp(130px, 24vw, 220px);
            aspect-ratio: 3 / 4;
            min-height: 0;
            place-items: center;
            overflow: hidden;
            border: 1px solid rgba(151, 104, 12, .26);
            border-radius: 8px;
            background:
                linear-gradient(180deg, rgba(255, 247, 207, .96), rgba(244, 211, 107, .94));
            color: #111111;
            font-weight: 900;
            letter-spacing: 0;
            opacity: 0;
            transform: translate(-50%, 48%) scale(.82) rotateX(8deg);
            transform-origin: center bottom;
            transition: transform .9s cubic-bezier(.16, 1, .3, 1), opacity .45s ease;
        }

        .award-winner-photo-card {
            position: absolute;
            top: 50%;
            left: 50%;
            z-index: 2;
            display: grid;
            grid-template-rows: minmax(0, 1fr) auto;
            width: min(92vw, 1120px);
            height: min(86vh, 800px);
            min-height: 0;
            overflow: hidden;
            border: 1px solid rgba(244, 211, 107, .7);
            border-radius: 8px;
            background:
                linear-gradient(180deg, rgba(255, 247, 207, .96), rgba(244, 211, 107, .94));
            box-shadow:
                0 34px 90px rgba(0, 0, 0, .54),
                0 0 0 1px rgba(255, 250, 240, .2),
                0 0 74px rgba(212, 175, 55, .28);
            opacity: 0;
            transform: translate(-50%, -8%) scale(.16) rotateX(10deg);
            transform-origin: center bottom;
            will-change: transform, opacity;
        }

        .award-letter span {
            border-top: 1px solid rgba(17, 17, 17, .18);
            border-bottom: 1px solid rgba(17, 17, 17, .18);
            padding: 8px 18px;
        }

        .award-letter-photo {
            position: relative;
            width: 100%;
            min-height: 0;
            overflow: hidden;
            background: rgba(17, 17, 17, .16);
        }

        .award-letter-photo::after {
            content: "";
            position: absolute;
            inset: 0;
            border: 1px solid rgba(255, 250, 240, .42);
            pointer-events: none;
        }

        .award-letter-photo img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: center;
        }

        .award-letter-info {
            display: grid;
            gap: 2px;
            width: 100%;
            min-width: 0;
            background:
                linear-gradient(180deg, rgba(255, 250, 240, .82), rgba(244, 211, 107, .92));
            padding: 8px 10px 10px;
            text-align: center;
        }

        .award-letter-caption {
            color: #6f4a04;
            font-size: clamp(.6rem, .8vw, .78rem);
            font-weight: 900;
            line-height: 1;
            text-transform: uppercase;
        }

        .award-letter-name {
            display: -webkit-box;
            overflow: hidden;
            color: #111111;
            font-size: clamp(.88rem, 1.18vw, 1.1rem);
            font-weight: 900;
            line-height: 1.04;
            overflow-wrap: anywhere;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
        }

        .award-envelope::before,
        .award-envelope::after {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: 8px;
        }

        .award-envelope::before {
            z-index: 4;
            clip-path: polygon(0 0, 50% 54%, 100% 0);
            background: linear-gradient(180deg, #fff2bc, #d8ae35);
            backface-visibility: hidden;
            transform-origin: top center;
            transition: transform .65s ease;
        }

        .award-envelope::after {
            z-index: 3;
            clip-path: polygon(0 100%, 50% 45%, 100% 100%);
            background: linear-gradient(180deg, rgba(151, 104, 12, .08), rgba(117, 78, 8, .28));
        }

        .award-slide.is-opening .award-envelope::before {
            animation: envelopeFlapOpen 5.2s cubic-bezier(.16, 1, .3, 1) forwards;
        }

        .award-slide.is-opening .award-letter {
            animation: awardWinnerPhotoReveal 5.2s cubic-bezier(.16, 1, .3, 1) forwards;
        }

        .award-slide.is-opening .award-winner-photo-card {
            animation: awardWinnerPosterReveal 5.2s cubic-bezier(.16, 1, .3, 1) forwards;
        }

        .award-slide.is-opening .nominee-result-gallery {
            filter: brightness(.72) saturate(.86);
        }

        .award-slide.is-revealed .envelope-panel {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transform: translateY(-14px);
        }

        .nominee-result-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(clamp(168px, 16vw, 245px), 1fr));
            grid-auto-rows: clamp(310px, 52vh, 540px);
            gap: clamp(12px, 1.5vw, 18px);
            align-content: start;
            align-items: stretch;
            min-height: 0;
            overflow: auto;
            padding: 2px;
            scrollbar-color: rgba(244, 211, 107, .58) rgba(255, 250, 240, .08);
            transition: filter .45s ease;
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
            filter: saturate(.92);
            opacity: .92;
            transform: translateY(0);
            transition: opacity .45s ease, filter .45s ease, transform .45s ease, border-color .45s ease, box-shadow .45s ease, background .45s ease;
        }

        .award-nominee-image {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 0;
            overflow: hidden;
            background: rgba(0, 0, 0, .28);
        }

        .award-nominee-image img {
            display: block;
            width: 100%;
            max-width: 100%;
            height: 100%;
            max-height: none;
            object-fit: cover;
            object-position: center;
        }

        .award-nominee-body {
            display: grid;
            gap: 6px;
            min-height: 92px;
            align-content: center;
            padding: 10px 12px 12px;
            text-align: center;
        }

        .award-nominee-body h3 {
            display: -webkit-box;
            margin: 0;
            overflow: hidden;
            color: #ffffff;
            font-size: clamp(.96rem, 1.22vw, 1.24rem);
            font-weight: 900;
            line-height: 1.08;
            overflow-wrap: anywhere;
            text-wrap: balance;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
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
            font-size: clamp(.86rem, 1vw, 1rem);
            font-weight: 900;
        }

        .winner-badge {
            display: none;
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

        @keyframes envelopeSceneArrive {
            0% {
                opacity: 0;
                transform: translateY(18px) scale(.9);
            }
            8% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
            46% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
            58% {
                opacity: 0;
                transform: translateY(14vh) scale(.86);
            }
            100% {
                opacity: 0;
                transform: translateY(18vh) scale(.82);
            }
        }

        @keyframes envelopeFlapOpen {
            0%,
            14% {
                transform: rotateX(0deg);
            }
            34%,
            100% {
                transform: rotateX(178deg);
            }
        }

        @keyframes awardWinnerPhotoReveal {
            0%,
            30% {
                opacity: 0;
                transform: translate(-50%, 48%) scale(.82) rotateX(8deg);
            }
            42% {
                opacity: 1;
                transform: translate(-50%, 18%) scale(.9) rotateX(0deg);
            }
            68% {
                opacity: 1;
                transform: translate(-50%, -48%) scale(1.05) rotateX(0deg);
            }
            84%,
            100% {
                opacity: 1;
                transform: translate(-50%, -54%) scale(1.08) rotateX(0deg);
            }
        }

        @keyframes awardWinnerPosterReveal {
            0%,
            24% {
                z-index: 2;
                opacity: 0;
                filter: brightness(.8) saturate(.9);
                transform: translate(-50%, -8%) scale(.16) rotateX(10deg);
            }
            34% {
                z-index: 2;
                opacity: 1;
                transform: translate(-50%, -30%) scale(.2) rotateX(0deg);
            }
            48% {
                z-index: 2;
                opacity: 1;
                transform: translate(-50%, -78%) scale(.28) rotateX(0deg);
            }
            56% {
                z-index: 7;
                opacity: 1;
                transform: translate(-50%, -64%) scale(.36) rotateX(0deg);
            }
            78% {
                z-index: 7;
                opacity: 1;
                filter: brightness(1) saturate(1);
                transform: translate(-50%, -50%) scale(1) rotateX(0deg);
            }
            100% {
                z-index: 7;
                opacity: 1;
                filter: brightness(1) saturate(1);
                transform: translate(-50%, -50%) scale(1) rotateX(0deg);
            }
        }

        .award-slide.is-revealed .award-score,
        .award-slide.is-revealed .winner-badge {
            opacity: 1;
            transform: translateY(0);
        }

        .award-slide.is-revealed .winner-badge {
            display: inline-flex;
        }

        .award-slide.is-revealed .award-nominee.is-winner {
            border-color: rgba(244, 211, 107, .96);
            background:
                linear-gradient(180deg, rgba(244, 211, 107, .12), rgba(255, 250, 240, .08)),
                rgba(255, 250, 240, .1);
            box-shadow:
                inset 0 0 0 2px rgba(244, 211, 107, .72),
                inset 0 0 0 8px rgba(212, 175, 55, .1);
            transform: translateY(0);
        }

        .award-slide.is-revealed .award-nominee.is-winner::before {
            content: "";
            position: absolute;
            inset: 8px;
            z-index: 1;
            border: 1px solid rgba(255, 250, 240, .24);
            border-radius: 6px;
            pointer-events: none;
        }

        .award-slide.is-revealed .award-nominee.is-winner .award-nominee-body {
            background:
                linear-gradient(180deg, rgba(212, 175, 55, .14), rgba(0, 0, 0, .08)),
                rgba(0, 0, 0, .18);
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
                grid-auto-rows: clamp(320px, 64vh, 520px);
                overflow: visible;
            }

            .oscar-stage-status,
            .award-action-button {
                width: 100%;
            }

            .award-envelope-scene {
                width: min(360px, 82vw);
            }

            .award-winner-photo-card {
                width: min(92vw, 760px);
                height: min(82vh, 720px);
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

            .award-envelope-scene {
                width: min(330px, 52vw);
            }

            .award-winner-photo-card {
                width: min(76vw, 880px);
                height: min(82vh, 620px);
            }

            .nominee-result-gallery {
                grid-auto-rows: clamp(210px, 47vh, 340px);
            }

            .award-nominee {
                grid-template-rows: minmax(0, 1fr) auto;
            }

            .award-nominee-body {
                min-height: 78px;
                padding: 8px 10px 10px;
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
                                @php
                                    $winnerNominee = $result['nominees']->first(fn ($nominee) => (bool) $nominee->is_oscar_winner);
                                @endphp

                                <div class="envelope-panel" aria-hidden="true">
                                    <div class="award-envelope-scene">
                                        <div class="award-envelope">
                                            @unless($winnerNominee)
                                                <div class="award-letter">
                                                    <span>Переможець</span>
                                                </div>
                                            @endunless
                                        </div>
                                    </div>
                                    @if($winnerNominee)
                                        <div class="award-winner-photo-card">
                                            <div class="award-letter-photo">
                                                <img src="{{ $winnerNominee->image_url }}" alt="">
                                            </div>
                                            <div class="award-letter-info">
                                                <div class="award-letter-caption">Переможець</div>
                                                <div class="award-letter-name">{{ $winnerNominee->name }}</div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="nominee-result-gallery">
                                    @foreach($result['nominees'] as $nominee)
                                        @php
                                            $isWinner = (bool) $nominee->is_oscar_winner;
                                        @endphp
                                        <article class="award-nominee {{ $isWinner ? 'is-winner' : '' }}">
                                            <div class="award-nominee-image">
                                                <img src="{{ $nominee->image_url }}" alt="{{ $nominee->name }}">
                                            </div>
                                            <div class="award-nominee-body">
                                                <h3>{{ $nominee->name }}</h3>
                                                @if($showScores)
                                                    <p class="award-score">{{ $nominee->oscar_score }} голосів</p>
                                                @endif
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
                }, 5200);
            });

            updateControls();
        });
    </script>
@endsection
