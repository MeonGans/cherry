@extends('layouts.app2')

@section('content')
    @php
        $teamStyles = [
            'Вогонь' => [
                'accent' => '#f97316',
                'accent2' => '#ef4444',
                'soft' => '#fff7ed',
                'line' => 'У команді Вогню важливі сміливість, швидкість і яскрава енергія.',
            ],
            'Повітря' => [
                'accent' => '#38bdf8',
                'accent2' => '#818cf8',
                'soft' => '#eff6ff',
                'line' => 'У команді Повітря важливі легкість, ідеї і вміння бачити шлях згори.',
            ],
            'Вода' => [
                'accent' => '#0ea5e9',
                'accent2' => '#06b6d4',
                'soft' => '#ecfeff',
                'line' => 'У команді Води важливі спокій, гнучкість і уважність до деталей.',
            ],
            'Земля' => [
                'accent' => '#22c55e',
                'accent2' => '#84cc16',
                'soft' => '#f0fdf4',
                'line' => 'У команді Землі важливі надійність, терпіння і сила підтримки.',
            ],
        ];
        $style = $teamStyles[$team->name] ?? [
            'accent' => '#6366f1',
            'accent2' => '#0ea5e9',
            'soft' => '#eef2ff',
            'line' => 'Команда готова прийняти нового учасника.',
        ];
        $imageId = match ($team->name) {
            'Вогонь' => 1,
            'Повітря' => 2,
            'Вода' => 3,
            'Земля' => 4,
            default => $team->id,
        };
    @endphp

    <style>
        @media (min-width: 1024px) {
            body:has(.element-result) .main-container .main-content {
                margin-left: 0 !important;
                margin-right: 0 !important;
            }
        }

        .element-result {
            --result-accent: {{ $style['accent'] }};
            --result-accent-2: {{ $style['accent2'] }};
            --result-soft: {{ $style['soft'] }};
            min-height: calc(100vh - 48px);
            margin: -1.5rem;
            display: grid;
            place-items: center;
            padding: clamp(18px, 4vw, 64px);
            color: #142033;
            background:
                linear-gradient(135deg, color-mix(in srgb, var(--result-accent) 16%, white), color-mix(in srgb, var(--result-accent-2) 18%, white)),
                url("{{ asset('assets/images/knowledge/pattern.png') }}");
            background-size: cover, 420px;
            font-family: Nunito, Arial, sans-serif;
        }

        .element-result-card {
            width: min(100%, 980px);
            display: grid;
            grid-template-columns: minmax(220px, 0.9fr) minmax(280px, 1.1fr);
            gap: clamp(22px, 5vw, 56px);
            align-items: center;
            border: 1px solid rgba(15, 23, 42, 0.1);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.92);
            box-shadow: 0 28px 80px rgba(15, 23, 42, 0.18);
            padding: clamp(22px, 5vw, 58px);
            overflow: hidden;
            position: relative;
        }

        .element-result-card::before {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background:
                linear-gradient(135deg, color-mix(in srgb, var(--result-accent) 14%, transparent), transparent 48%),
                linear-gradient(315deg, color-mix(in srgb, var(--result-accent-2) 14%, transparent), transparent 46%);
        }

        .element-result-visual,
        .element-result-copy {
            position: relative;
            z-index: 1;
        }

        .element-result-visual {
            display: grid;
            place-items: center;
        }

        .element-result-ring {
            width: min(68vw, 310px);
            aspect-ratio: 1;
            display: grid;
            place-items: center;
            border-radius: 50%;
            background:
                conic-gradient(from 0deg, var(--result-accent), var(--result-accent-2), var(--result-accent));
            box-shadow:
                0 24px 64px color-mix(in srgb, var(--result-accent) 24%, transparent),
                inset 0 0 0 12px rgba(255, 255, 255, 0.28);
        }

        .element-result-ring img {
            width: 78%;
            height: 78%;
            border-radius: 50%;
            object-fit: contain;
            background: rgba(255, 255, 255, 0.9);
            padding: 18px;
            box-shadow: inset 0 0 0 1px rgba(15, 23, 42, 0.08);
        }

        .element-result-kicker {
            margin: 0 0 12px;
            color: color-mix(in srgb, var(--result-accent) 80%, #0f172a);
            font-size: 12px;
            font-weight: 1000;
            letter-spacing: 0.14em;
            text-transform: uppercase;
        }

        .element-result-copy h1 {
            margin: 0;
            color: #0f172a;
            font-size: clamp(42px, 8vw, 92px);
            font-weight: 1000;
            line-height: 0.9;
            overflow-wrap: anywhere;
        }

        .element-result-name {
            width: fit-content;
            margin: 18px 0 22px;
            border: 1px solid color-mix(in srgb, var(--result-accent) 36%, transparent);
            border-radius: 8px;
            background: color-mix(in srgb, var(--result-accent) 12%, white);
            color: color-mix(in srgb, var(--result-accent) 72%, #0f172a);
            font-size: clamp(1.25rem, 3vw, 2rem);
            font-weight: 1000;
            padding: 9px 14px;
        }

        .element-result-text {
            max-width: 560px;
            color: #334155;
            font-size: clamp(1.05rem, 2vw, 1.35rem);
            font-weight: 800;
            line-height: 1.55;
        }

        .element-result-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 28px;
        }

        .element-result-button {
            min-height: 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--result-accent), var(--result-accent-2));
            color: #ffffff;
            font-weight: 1000;
            padding: 0 18px;
            text-decoration: none;
            transition: transform 160ms ease, box-shadow 160ms ease;
        }

        .element-result-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 24px color-mix(in srgb, var(--result-accent) 24%, transparent);
        }

        @media (max-width: 760px) {
            .element-result-card {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .element-result-name {
                margin-left: auto;
                margin-right: auto;
            }

            .element-result-text {
                margin: 0 auto;
            }

            .element-result-actions {
                justify-content: center;
            }
        }
    </style>

    <main class="element-result">
        <section class="element-result-card">
            <div class="element-result-visual" aria-hidden="true">
                <div class="element-result-ring">
                    <img src="{{ asset('assets/images/elements/'.$imageId.'.png') }}" alt="">
                </div>
            </div>

            <div class="element-result-copy">
                <p class="element-result-kicker">Стихія визначена</p>
                <h1>Команда</h1>
                <div class="element-result-name">{{ $team->name }}</div>
                <p class="element-result-text">{{ $style['line'] }}</p>

                <div class="element-result-actions">
                    <a href="{{ route('test.show') }}" class="element-result-button">Наступне сортування</a>
                </div>
            </div>
        </section>
    </main>
@endsection
