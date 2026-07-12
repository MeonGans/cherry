@extends('layouts.app2')

@section('content')
    <style>
        body:has(.photo-awards-stage) {
            background: #100f12;
            overflow-x: hidden;
        }

        body:has(.photo-awards-stage) .main-container,
        body:has(.photo-awards-stage) .main-container .main-content {
            width: 100vw !important;
            max-width: none !important;
            min-width: 0 !important;
            margin-right: 0 !important;
            margin-left: 0 !important;
            padding-right: 0 !important;
            padding-left: 0 !important;
        }

        body:has(.photo-awards-stage) .dvanimation,
        body:has(.photo-awards-stage) [x-data="basic"] {
            width: 100vw;
            min-height: 100svh;
            padding: 0 !important;
        }

        body:has(.photo-awards-stage) .fixed.bottom-6 {
            display: none !important;
        }

        .photo-awards-stage {
            --stage-bg: #100f12;
            --stage-panel: rgba(255, 255, 255, .075);
            --stage-line: rgba(255, 255, 255, .16);
            --stage-ink: #fffaf4;
            --stage-muted: rgba(255, 250, 244, .68);
            --stage-red: #d9234e;
            --stage-gold: #f2c94c;
            box-sizing: border-box;
            display: grid;
            width: 100%;
            max-width: 100vw;
            min-height: 100svh;
            overflow-x: clip;
            background:
                linear-gradient(135deg, rgba(217, 35, 78, .2), transparent 34%),
                linear-gradient(315deg, rgba(242, 201, 76, .13), transparent 32%),
                var(--stage-bg);
            color: var(--stage-ink);
            padding: clamp(14px, 2vw, 26px);
        }

        .photo-stage-frame {
            display: grid;
            gap: 18px;
            width: min(100%, 1540px);
            min-width: 0;
            max-width: 100%;
            margin: 0 auto;
        }

        .photo-stage-header,
        .photo-stage-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            border: 1px solid var(--stage-line);
            border-radius: 8px;
            background: rgba(0, 0, 0, .24);
            padding: clamp(14px, 2vw, 20px);
        }

        .photo-kicker,
        .photo-stage-status {
            margin: 0;
            color: var(--stage-muted);
            font-weight: 900;
            letter-spacing: 0;
            text-transform: uppercase;
        }

        .photo-kicker {
            font-size: .78rem;
        }

        .photo-stage-title {
            min-width: 0;
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
            border: 1px solid rgba(242, 201, 76, .48);
            border-radius: 999px;
            background: rgba(242, 201, 76, .14);
            color: var(--stage-gold);
            font-size: clamp(1.5rem, 3vw, 2.6rem);
            font-weight: 900;
        }

        .photo-results-empty {
            display: grid;
            min-height: 50svh;
            place-items: center;
            border: 1px dashed rgba(255, 255, 255, .22);
            border-radius: 8px;
            background: var(--stage-panel);
            color: var(--stage-muted);
            font-size: 1.2rem;
            font-weight: 900;
            padding: 24px;
            text-align: center;
        }

        .photo-result-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            grid-auto-flow: dense;
            gap: 16px;
            align-items: stretch;
            min-width: 0;
            max-width: 100%;
            overflow-x: clip;
        }

        .photo-result-card {
            position: relative;
            display: grid;
            min-width: 0;
            overflow: hidden;
            border: 1px solid var(--stage-line);
            border-radius: 8px;
            background: rgba(255, 255, 255, .08);
            color: inherit;
            cursor: pointer;
            padding: 0;
            text-align: left;
            transition: transform .28s ease, opacity .28s ease, border-color .28s ease, box-shadow .28s ease, filter .28s ease;
        }

        .photo-result-card img {
            display: block;
            width: 100%;
            aspect-ratio: 4 / 3;
            background: rgba(0, 0, 0, .28);
            object-fit: contain;
        }

        .photo-result-card:hover,
        .photo-result-card:focus-visible {
            border-color: rgba(242, 201, 76, .78);
            box-shadow: 0 18px 48px rgba(0, 0, 0, .28);
            outline: none;
            transform: translateY(-3px);
        }

        .photo-card-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            min-height: 50px;
            padding: 10px 12px;
        }

        .photo-card-number,
        .score {
            display: inline-flex;
            min-height: 30px;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            font-weight: 900;
            padding: 0 10px;
            white-space: nowrap;
        }

        .photo-card-number {
            background: rgba(255, 255, 255, .12);
            color: var(--stage-ink);
        }

        .score {
            background: rgba(242, 201, 76, .16);
            color: var(--stage-gold);
        }

        .winner-detail {
            display: none;
            margin: 0;
            color: #ffffff;
            font-size: clamp(1.1rem, 2vw, 1.6rem);
            font-weight: 900;
            line-height: 1.2;
        }

        .photo-result-card.dimmed {
            filter: grayscale(.9) saturate(.45);
            opacity: .36;
        }

        .photo-result-card.is-shuffling {
            transform: rotate(var(--spin, 1deg)) scale(.97);
        }

        .photo-result-card.winner-revealed {
            grid-column: span 2;
            border-color: rgba(242, 201, 76, .96);
            box-shadow: 0 28px 90px rgba(242, 201, 76, .22);
            filter: none;
            opacity: 1;
            transform: none;
            z-index: 2;
        }

        .photo-result-card.winner-revealed img {
            aspect-ratio: 16 / 10;
        }

        .photo-result-card.winner-revealed .winner-detail {
            display: block;
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
            background: var(--stage-red);
            color: #ffffff;
            cursor: pointer;
            font: inherit;
            font-weight: 900;
            padding: 0 22px;
            white-space: nowrap;
            transition: background .2s ease, opacity .2s ease, transform .2s ease;
        }

        .photo-reveal-button:hover:not(:disabled) {
            background: #b4173d;
            transform: translateY(-1px);
        }

        .photo-reveal-button:disabled {
            cursor: default;
            opacity: .74;
        }

        .photo-lightbox {
            position: fixed;
            inset: 0;
            display: none;
            grid-template-rows: auto minmax(0, 1fr) auto;
            gap: 16px;
            background: rgba(0, 0, 0, .88);
            padding: 18px;
            z-index: 9999;
        }

        .photo-lightbox.is-open {
            display: grid;
        }

        .photo-lightbox-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            min-width: 0;
        }

        .photo-lightbox-counter {
            display: inline-flex;
            min-height: 40px;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255, 255, 255, .22);
            border-radius: 999px;
            background: rgba(255, 255, 255, .1);
            color: #ffffff;
            font-weight: 900;
            padding: 0 16px;
            white-space: nowrap;
        }

        .photo-lightbox-viewer {
            position: relative;
            display: grid;
            min-height: 0;
            place-items: center;
        }

        .photo-lightbox-viewer img {
            display: block;
            max-width: min(100%, 1300px);
            max-height: calc(100svh - 190px);
            object-fit: contain;
        }

        .photo-lightbox-close {
            display: grid;
            width: 44px;
            height: 44px;
            place-items: center;
            border: 1px solid rgba(255, 255, 255, .28);
            border-radius: 999px;
            background: rgba(255, 255, 255, .12);
            color: #ffffff;
            cursor: pointer;
            font: inherit;
            font-size: 26px;
            line-height: 1;
        }

        .photo-lightbox-nav {
            position: absolute;
            top: 50%;
            display: grid;
            width: clamp(46px, 6vw, 64px);
            aspect-ratio: 1;
            place-items: center;
            border: 1px solid rgba(255, 255, 255, .28);
            border-radius: 999px;
            background: rgba(0, 0, 0, .42);
            color: #ffffff;
            cursor: pointer;
            font: inherit;
            font-size: clamp(24px, 4vw, 34px);
            line-height: 1;
            transform: translateY(-50%);
            transition: background .18s ease, transform .18s ease, opacity .18s ease;
            z-index: 2;
        }

        .photo-lightbox-nav:hover:not(:disabled),
        .photo-lightbox-nav:focus-visible {
            background: rgba(217, 35, 78, .84);
            outline: none;
            transform: translateY(-50%) scale(1.04);
        }

        .photo-lightbox-nav:disabled {
            cursor: default;
            opacity: .34;
        }

        .photo-lightbox-prev {
            left: clamp(8px, 2vw, 24px);
        }

        .photo-lightbox-next {
            right: clamp(8px, 2vw, 24px);
        }

        .photo-lightbox-thumbs {
            display: flex;
            gap: 10px;
            min-width: 0;
            overflow-x: auto;
            padding: 2px 2px 8px;
            scrollbar-color: rgba(242, 201, 76, .7) rgba(255, 255, 255, .12);
        }

        .photo-lightbox-thumb {
            flex: 0 0 auto;
            width: clamp(68px, 8vw, 108px);
            overflow: hidden;
            border: 2px solid transparent;
            border-radius: 8px;
            background: rgba(255, 255, 255, .12);
            cursor: pointer;
            padding: 0;
            opacity: .62;
            transition: border-color .18s ease, opacity .18s ease, transform .18s ease;
        }

        .photo-lightbox-thumb img {
            display: block;
            width: 100%;
            aspect-ratio: 4 / 3;
            object-fit: cover;
        }

        .photo-lightbox-thumb:hover,
        .photo-lightbox-thumb:focus-visible,
        .photo-lightbox-thumb.is-active {
            border-color: var(--stage-gold);
            opacity: 1;
            outline: none;
            transform: translateY(-1px);
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

            .photo-result-card.winner-revealed {
                grid-column: span 1;
            }

            .photo-lightbox {
                gap: 12px;
                padding: 12px;
            }

            .photo-lightbox-viewer img {
                max-height: calc(100svh - 176px);
            }

            .photo-lightbox-nav {
                top: auto;
                bottom: 8px;
                transform: none;
            }

            .photo-lightbox-nav:hover:not(:disabled),
            .photo-lightbox-nav:focus-visible {
                transform: scale(1.04);
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
                    Фінальні фото ще не обрано.
                </div>
            @else
                <div id="photoResults" class="photo-result-gallery">
                    @foreach($photos as $photo)
                        @php
                            $isWinner = $winnerPhoto && (int) $winnerPhoto->id === (int) $photo->id;
                            $ownerName = $photo->user?->name ?? 'Автор не вказаний';
                        @endphp
                        <button
                            type="button"
                            class="photo-result-card {{ $isWinner ? 'is-winner' : '' }}"
                            style="--spin: {{ $loop->odd ? '-1.8deg' : '1.8deg' }};"
                            data-owner="{{ $ownerName }}"
                            data-score="{{ $photo->score }}"
                            data-image="{{ asset($photo->image_path) }}"
                            data-index="{{ $loop->index }}"
                            aria-label="Відкрити фото {{ $loop->iteration }}"
                        >
                            <img src="{{ asset($photo->image_path) }}" alt="Фото учасника">
                            <div class="photo-card-meta">
                                <span class="photo-card-number">Фото {{ $loop->iteration }}</span>
                                @if($showScores)
                                    <span class="score">{{ $photo->score }} балів</span>
                                @endif
                                <p class="winner-detail">Переможець: <span class="winner-owner">{{ $ownerName }}</span></p>
                            </div>
                        </button>
                    @endforeach
                </div>

                <footer class="photo-stage-footer">
                    <p id="resultStatus" class="photo-stage-status">Галерея відкрита</p>
                    <button id="revealPhotoResult" class="photo-reveal-button" type="button">Результат</button>
                </footer>
            @endif
        </div>
    </section>

    <div id="photoLightbox" class="photo-lightbox" aria-hidden="true">
        <div class="photo-lightbox-topbar">
            <div id="photoLightboxCounter" class="photo-lightbox-counter">0 / {{ $photos->count() }}</div>
            <button id="photoLightboxClose" class="photo-lightbox-close" type="button" aria-label="Закрити">&times;</button>
        </div>

        <div class="photo-lightbox-viewer">
            <button id="photoLightboxPrev" class="photo-lightbox-nav photo-lightbox-prev" type="button" aria-label="Попереднє фото">&#10094;</button>
            <img id="photoLightboxImage" src="" alt="Перегляд фото">
            <button id="photoLightboxNext" class="photo-lightbox-nav photo-lightbox-next" type="button" aria-label="Наступне фото">&#10095;</button>
        </div>

        <div id="photoLightboxThumbs" class="photo-lightbox-thumbs" aria-label="Мініатюри фото">
            @foreach($photos as $photo)
                <button class="photo-lightbox-thumb" type="button" data-index="{{ $loop->index }}" aria-label="Перейти до фото {{ $loop->iteration }}">
                    <img src="{{ asset($photo->image_path) }}" alt="">
                </button>
            @endforeach
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const stage = document.getElementById('photoAwardsStage');
            const gallery = document.getElementById('photoResults');
            const button = document.getElementById('revealPhotoResult');
            const status = document.getElementById('resultStatus');
            const cards = Array.from(document.querySelectorAll('.photo-result-card'));
            const lightbox = document.getElementById('photoLightbox');
            const lightboxImage = document.getElementById('photoLightboxImage');
            const lightboxClose = document.getElementById('photoLightboxClose');
            const lightboxPrev = document.getElementById('photoLightboxPrev');
            const lightboxNext = document.getElementById('photoLightboxNext');
            const lightboxCounter = document.getElementById('photoLightboxCounter');
            const thumbs = Array.from(document.querySelectorAll('.photo-lightbox-thumb'));
            let lightboxIndex = 0;

            stage?.addEventListener('contextmenu', (event) => event.preventDefault());

            const syncLightbox = (index) => {
                if (!lightboxImage || cards.length === 0) {
                    return;
                }

                const targetIndex = Number.isFinite(index) ? index : 0;
                lightboxIndex = (targetIndex + cards.length) % cards.length;
                const card = cards[lightboxIndex];

                lightboxImage.src = card.dataset.image;

                if (lightboxCounter) {
                    lightboxCounter.textContent = `${lightboxIndex + 1} / ${cards.length}`;
                }

                const hasMultiplePhotos = cards.length > 1;
                lightboxPrev.disabled = !hasMultiplePhotos;
                lightboxNext.disabled = !hasMultiplePhotos;

                thumbs.forEach((thumb, thumbIndex) => {
                    const isActive = thumbIndex === lightboxIndex;
                    thumb.classList.toggle('is-active', isActive);
                    thumb.setAttribute('aria-current', isActive ? 'true' : 'false');
                });

                thumbs[lightboxIndex]?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest',
                    inline: 'center',
                });
            };

            const openLightbox = (card) => {
                if (!lightbox || !lightboxImage) {
                    return;
                }

                const index = Number.parseInt(card.dataset.index, 10);
                syncLightbox(Number.isNaN(index) ? cards.indexOf(card) : index);
                lightbox.classList.add('is-open');
                lightbox.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            };

            const closeLightbox = () => {
                if (!lightbox || !lightboxImage) {
                    return;
                }

                lightbox.classList.remove('is-open');
                lightbox.setAttribute('aria-hidden', 'true');
                lightboxImage.src = '';
                document.body.style.overflow = '';
            };

            const showPreviousPhoto = () => syncLightbox(lightboxIndex - 1);
            const showNextPhoto = () => syncLightbox(lightboxIndex + 1);

            cards.forEach((card) => {
                card.addEventListener('click', () => openLightbox(card));
            });

            lightboxClose?.addEventListener('click', closeLightbox);
            lightboxPrev?.addEventListener('click', (event) => {
                event.stopPropagation();
                showPreviousPhoto();
            });
            lightboxNext?.addEventListener('click', (event) => {
                event.stopPropagation();
                showNextPhoto();
            });
            thumbs.forEach((thumb) => {
                thumb.addEventListener('click', (event) => {
                    event.stopPropagation();
                    syncLightbox(Number.parseInt(thumb.dataset.index, 10));
                });
            });
            lightbox?.addEventListener('click', (event) => {
                if (event.target === lightbox) {
                    closeLightbox();
                }
            });
            document.addEventListener('keydown', (event) => {
                if (!lightbox?.classList.contains('is-open')) {
                    return;
                }

                if (event.key === 'Escape') {
                    closeLightbox();
                }

                if (event.key === 'ArrowLeft') {
                    showPreviousPhoto();
                }

                if (event.key === 'ArrowRight') {
                    showNextPhoto();
                }
            });

            if (!button || !gallery || cards.length === 0) {
                return;
            }

            const shuffle = (items) => {
                const shuffled = [...items];

                for (let index = shuffled.length - 1; index > 0; index -= 1) {
                    const randomIndex = Math.floor(Math.random() * (index + 1));
                    [shuffled[index], shuffled[randomIndex]] = [shuffled[randomIndex], shuffled[index]];
                }

                return shuffled;
            };

            const renderOrder = (items) => {
                items.forEach((card) => gallery.appendChild(card));
            };

            const revealWinner = () => {
                const winner = document.querySelector('.photo-result-card.is-winner');

                cards.forEach((card) => card.classList.add('dimmed'));

                if (winner) {
                    winner.classList.remove('dimmed');
                    winner.classList.add('winner-revealed');
                    gallery.prepend(winner);

                    if (status) {
                        status.textContent = `Переможець: ${winner.dataset.owner}`;
                    }

                    window.setTimeout(() => {
                        winner.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'nearest' });
                        window.scrollTo({ left: 0 });
                    }, 120);
                } else if (status) {
                    status.textContent = 'Переможця ще немає';
                }

                button.textContent = 'Результат відкрито';
                button.disabled = true;
            };

            button.addEventListener('click', () => {
                button.disabled = true;
                button.textContent = 'Перемішуємо...';

                if (status) {
                    status.textContent = 'Фото перемішуються';
                }

                let rounds = 0;
                const timer = window.setInterval(() => {
                    cards.forEach((card) => card.classList.add('is-shuffling'));
                    renderOrder(shuffle(cards));

                    window.setTimeout(() => {
                        cards.forEach((card) => card.classList.remove('is-shuffling'));
                    }, 180);

                    rounds += 1;

                    if (rounds >= 6) {
                        window.clearInterval(timer);
                        window.setTimeout(revealWinner, 260);
                    }
                }, 280);
            });
        });
    </script>
@endsection
