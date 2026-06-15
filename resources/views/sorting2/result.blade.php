@extends('layouts.app2')

@section('content')
    <style>
        .sorting2-result {
            min-height: calc(100vh - 48px);
            display: grid;
            place-items: center;
            padding: 24px;
            color: #172033;
            background:
                linear-gradient(135deg, rgba(248, 250, 252, 0.96), rgba(236, 254, 255, 0.9) 52%, rgba(255, 247, 237, 0.95)),
                url("{{ asset('assets/images/knowledge/pattern.png') }}");
            background-size: cover, 420px;
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 8px;
        }

        .sorting2-result-panel {
            width: min(760px, 100%);
            border: 1px solid rgba(15, 23, 42, 0.1);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.92);
            padding: 30px;
            text-align: center;
            box-shadow: 0 24px 58px rgba(15, 23, 42, 0.16);
            backdrop-filter: blur(10px);
        }

        .sorting2-result-image-wrap {
            width: min(360px, 82vw);
            aspect-ratio: 1;
            display: grid;
            place-items: center;
            margin: 0 auto 20px;
            border: 1px solid rgba(15, 23, 42, 0.12);
            border-radius: 8px;
            background: #f8fafc;
        }

        .sorting2-result-image {
            width: 86%;
            height: 86%;
            object-fit: contain;
            filter: drop-shadow(0 18px 28px rgba(15, 23, 42, 0.18));
            animation: sorting2Reveal 420ms ease-out both;
        }

        .sorting2-result-kicker {
            display: inline-flex;
            align-items: center;
            min-height: 32px;
            border-radius: 8px;
            background: #f8fafc;
            padding: 0 12px;
            color: var(--sorting-accent);
            font-weight: 900;
        }

        .sorting2-result-title {
            margin: 18px 0 0;
            font-size: 2.4rem;
            line-height: 1.08;
            font-weight: 900;
            color: #0f172a;
        }

        .sorting2-result-team {
            color: var(--sorting-accent);
        }

        .sorting2-result-copy {
            max-width: 560px;
            margin: 14px auto 0;
            color: #475569;
            font-size: 1.05rem;
            line-height: 1.65;
        }

        .sorting2-result-actions {
            display: flex;
            justify-content: center;
            margin-top: 24px;
        }

        .sorting2-result-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            border-radius: 8px;
            background: #0f172a;
            padding: 0 18px;
            color: #ffffff;
            font-weight: 900;
            transition: transform 160ms ease, box-shadow 160ms ease;
        }

        .sorting2-result-button:hover {
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.16);
        }

        @keyframes sorting2Reveal {
            from {
                opacity: 0;
                transform: scale(0.92) rotate(-2deg);
            }
            to {
                opacity: 1;
                transform: scale(1) rotate(0);
            }
        }

        @media (max-width: 640px) {
            .sorting2-result {
                padding: 14px;
            }

            .sorting2-result-panel {
                padding: 18px;
            }

            .sorting2-result-title {
                font-size: 1.75rem;
            }

            .sorting2-result-copy {
                font-size: 1rem;
            }

            .sorting2-result-button {
                width: 100%;
            }
        }
    </style>

    <div class="sorting2-result" style="--sorting-accent: {{ $accent }};">
        <section class="sorting2-result-panel">
            <div class="sorting2-result-image-wrap">
                <img
                    class="sorting2-result-image"
                    src="{{ asset('assets/images/elements/' . $imageId . '.png') }}"
                    alt="{{ $team->name }}"
                >
            </div>

            <div class="sorting2-result-kicker">Сортування завершено</div>
            <h1 class="sorting2-result-title">
                Ваша стихія: <span class="sorting2-result-team">{{ $team->name }}</span>
            </h1>
            <p class="sorting2-result-copy">Команда вже зафіксована. Можна передавати естафету наступному учаснику.</p>

            <div class="sorting2-result-actions">
                <a class="sorting2-result-button" href="{{ route('sorting2.show') }}">Наступне сортування</a>
            </div>
        </section>
    </div>
@endsection
