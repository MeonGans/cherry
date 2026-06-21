@extends('layouts.users.app')

@section('content')
    <div class="mt-8 mb-5">
        <div class="mb-6 text-center">
            <h1 class="text-3xl font-black text-black dark:text-white">{{ $vote->name }}</h1>
            <p id="resultStatus" class="mt-2 text-base font-semibold text-white-dark">Результати приховано до натискання кнопки.</p>
        </div>

        @if($photos->isEmpty())
            <div class="panel text-center">
                <p>Фото ще не завантажено.</p>
            </div>
        @else
            <div id="photoResults" class="photo-results">
                @foreach($photos as $photo)
                    @php
                        $isWinner = $maxVotes > 0 && $photo->score === $maxVotes;
                    @endphp
                    <article class="photo-result-card {{ $isWinner ? 'is-winner' : '' }}" data-score="{{ $photo->score }}">
                        <div class="image-wrap">
                            <img src="{{ asset($photo->image_path) }}" alt="{{ $photo->title ?? 'Фото ' . $loop->iteration }}">
                        </div>
                        <div class="photo-result-body">
                            <h2>{{ $photo->title ?? 'Фото ' . $loop->iteration }}</h2>
                            <p class="score">{{ $photo->score }} балів</p>
                            @if($isWinner)
                                <p class="winner-label">Переможець</p>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>

            <button id="revealPhotoResult" class="btn btn-primary mx-auto mt-8 block">Результат</button>
        @endif
    </div>
@endsection

@section('styles')
    <style>
        .photo-results {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 18px;
            max-width: 1180px;
            margin: 0 auto;
        }

        .photo-result-card {
            overflow: hidden;
            border: 1px solid #e0e6ed;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 8px 20px rgba(31, 45, 61, .08);
            filter: grayscale(.55);
            opacity: .7;
            transform: translateY(10px);
            transition: transform .55s ease, opacity .55s ease, filter .55s ease, box-shadow .55s ease, border-color .55s ease;
        }

        .photo-result-card .image-wrap {
            background: #f1f2f3;
        }

        .photo-result-card img {
            display: block;
            width: 100%;
            aspect-ratio: 4 / 3;
            object-fit: cover;
        }

        .photo-result-body {
            padding: 12px;
            text-align: center;
        }

        .photo-result-body h2 {
            margin: 0;
            font-size: 1rem;
            font-weight: 800;
        }

        .score {
            margin-top: 8px;
            font-size: 1.1rem;
            font-weight: 900;
            color: #4361ee;
            opacity: 0;
            transform: translateY(6px);
            transition: opacity .4s ease, transform .4s ease;
        }

        .winner-label {
            display: none;
            margin-top: 8px;
            border-radius: 999px;
            background: #00ab55;
            padding: 5px 10px;
            color: #fff;
            font-weight: 900;
        }

        .photo-result-card.revealed {
            filter: grayscale(0);
            opacity: 1;
            transform: translateY(0);
        }

        .photo-result-card.revealed .score {
            opacity: 1;
            transform: translateY(0);
        }

        .photo-result-card.revealed.is-winner {
            border-color: #00ab55;
            box-shadow: 0 18px 36px rgba(0, 171, 85, .24);
            transform: translateY(-6px) scale(1.02);
        }

        .photo-result-card.revealed.is-winner .winner-label {
            display: inline-block;
        }
    </style>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const button = document.getElementById('revealPhotoResult');
            const status = document.getElementById('resultStatus');
            const cards = Array.from(document.querySelectorAll('.photo-result-card'));

            if (!button || cards.length === 0) {
                return;
            }

            button.addEventListener('click', () => {
                button.disabled = true;
                button.textContent = 'Відкриваємо...';
                status.textContent = 'Рахунок відкривається поступово.';

                cards.forEach((card, index) => {
                    setTimeout(() => {
                        card.classList.add('revealed');

                        if (index === cards.length - 1) {
                            status.textContent = 'Переможця відкрито.';
                            button.remove();
                        }
                    }, index * 900);
                });
            });
        });
    </script>
@endsection
