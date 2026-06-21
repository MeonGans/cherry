@extends('layouts.users.app')

@section('content')
    <div class="oscar-results-shell">
        <div class="award-stage">
            <p>Cherry Camp Awards</p>
            <h1>{{ $vote->name }}</h1>
        </div>

        @foreach($results as $key => $result)
            <section class="award-category" data-category="{{ $key }}">
                <div class="award-category-head">
                    <div>
                        <p>Номінація</p>
                        <h2>{{ $result['title'] }}</h2>
                    </div>
                    @if($result['nominees']->isNotEmpty())
                        <button type="button" class="btn btn-warning reveal-winner">Переможець</button>
                    @endif
                </div>

                @if($result['nominees']->isEmpty())
                    <div class="empty-award">У цій номінації ще немає голосів.</div>
                @else
                    <div class="nominee-result-grid">
                        @foreach($result['nominees'] as $nominee)
                            @php
                                $isWinner = $result['maxScore'] > 0 && $nominee->oscar_score === $result['maxScore'];
                            @endphp
                            <article class="award-nominee {{ $isWinner ? 'is-winner' : '' }}">
                                <img src="{{ $nominee->image_url }}" alt="{{ $nominee->name }}">
                                <div class="award-nominee-body">
                                    <h3>{{ $nominee->name }}</h3>
                                    <p class="award-score">{{ $nominee->oscar_score }} голосів</p>
                                    @if($isWinner)
                                        <p class="winner-badge">Переможець</p>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                    <p class="award-status">Конверт ще запечатаний.</p>
                @endif
            </section>
        @endforeach
    </div>
@endsection

@section('styles')
    <style>
        .oscar-results-shell {
            max-width: 1200px;
            margin: 0 auto;
        }

        .award-stage {
            margin-bottom: 30px;
            border-radius: 8px;
            background: linear-gradient(135deg, #101010, #2a210f 55%, #090909);
            padding: 32px 20px;
            text-align: center;
            color: #fff;
            box-shadow: 0 22px 50px rgba(0, 0, 0, .22);
        }

        .award-stage p {
            margin-bottom: 8px;
            color: #f4d36b;
            font-size: .9rem;
            font-weight: 900;
            text-transform: uppercase;
        }

        .award-stage h1 {
            font-size: 2.4rem;
            font-weight: 900;
        }

        .award-category {
            margin-bottom: 28px;
            overflow: hidden;
            border: 1px solid #e7cf83;
            border-radius: 8px;
            background: #fffdf7;
            box-shadow: 0 14px 32px rgba(104, 75, 6, .1);
        }

        .award-category-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            border-bottom: 1px solid #ead8a6;
            background: #151515;
            padding: 18px;
            color: #fff;
        }

        .award-category-head p {
            margin-bottom: 4px;
            color: #f4d36b;
            font-size: .78rem;
            font-weight: 900;
            text-transform: uppercase;
        }

        .award-category-head h2 {
            font-size: 1.45rem;
            font-weight: 900;
        }

        .nominee-result-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(165px, 1fr));
            gap: 16px;
            padding: 18px;
        }

        .award-nominee {
            overflow: hidden;
            border: 2px solid #ead8a6;
            border-radius: 8px;
            background: #fff;
            filter: saturate(.7);
            opacity: .72;
            transform: translateY(8px);
            transition: opacity .45s ease, filter .45s ease, transform .45s ease, border-color .45s ease, box-shadow .45s ease;
        }

        .award-nominee img {
            display: block;
            width: 100%;
            aspect-ratio: 1 / 1;
            object-fit: cover;
            background: #f5edd4;
        }

        .award-nominee-body {
            padding: 12px;
            text-align: center;
        }

        .award-nominee-body h3 {
            color: #111827;
            font-size: 1rem;
            font-weight: 900;
        }

        .award-score {
            margin-top: 8px;
            color: #a07800;
            font-weight: 900;
            opacity: 0;
            transform: translateY(6px);
            transition: opacity .35s ease, transform .35s ease;
        }

        .winner-badge {
            display: none;
            margin-top: 8px;
            border-radius: 999px;
            background: #d4af37;
            padding: 6px 10px;
            color: #111;
            font-weight: 900;
        }

        .award-category.is-thinking .award-status {
            color: #a07800;
        }

        .award-category.is-revealed .award-nominee {
            opacity: 1;
            filter: saturate(1);
            transform: translateY(0);
        }

        .award-category.is-revealed .award-score {
            opacity: 1;
            transform: translateY(0);
        }

        .award-category.is-revealed .award-nominee.is-winner {
            border-color: #d4af37;
            box-shadow: 0 20px 38px rgba(212, 175, 55, .28);
            transform: translateY(-6px) scale(1.03);
        }

        .award-category.is-revealed .award-nominee.is-winner .winner-badge {
            display: inline-block;
        }

        .award-status,
        .empty-award {
            padding: 0 18px 18px;
            color: #6b7280;
            font-weight: 800;
            text-align: center;
        }

        .empty-award {
            padding-top: 18px;
        }

        @media (max-width: 640px) {
            .award-category-head {
                align-items: flex-start;
                flex-direction: column;
            }

            .award-stage h1 {
                font-size: 1.9rem;
            }
        }
    </style>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.award-category').forEach((category) => {
                const button = category.querySelector('.reveal-winner');
                const status = category.querySelector('.award-status');

                if (!button) {
                    return;
                }

                button.addEventListener('click', () => {
                    button.disabled = true;
                    category.classList.add('is-thinking');
                    status.textContent = 'Відкриваємо конверт...';

                    setTimeout(() => {
                        category.classList.remove('is-thinking');
                        category.classList.add('is-revealed');
                        status.textContent = 'Переможця оголошено.';
                        button.remove();
                    }, 2200);
                });
            });
        });
    </script>
@endsection
