@extends('layouts.vote')

@section('title', $vote->name . ' - CHERRY CAMP')

@section('styles')
    <style>
        .element-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(148px, 1fr));
            gap: 14px;
            margin-top: 26px;
        }

        .element-option {
            position: relative;
            min-width: 0;
        }

        .element-input {
            position: absolute;
            width: 1px;
            height: 1px;
            opacity: 0;
            pointer-events: none;
        }

        .element-card {
            position: relative;
            display: grid;
            min-height: 190px;
            align-content: start;
            justify-items: center;
            gap: 10px;
            overflow: hidden;
            border: 1px solid var(--vote-line);
            border-radius: 18px;
            background: #ffffff;
            cursor: pointer;
            padding: 18px 14px;
            text-align: center;
            transition: border-color 160ms ease, box-shadow 160ms ease, transform 160ms ease;
        }

        .element-card::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, color-mix(in srgb, var(--team-color, #d9234e) 13%, #ffffff), #ffffff 48%);
            opacity: 0.88;
        }

        .element-logo,
        .element-name,
        .element-meta,
        .element-check {
            position: relative;
            z-index: 1;
        }

        .element-logo {
            display: grid;
            width: 86px;
            height: 86px;
            place-items: center;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.72);
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.76), 0 12px 26px rgba(21, 24, 39, 0.12);
        }

        .element-logo img {
            display: block;
            width: 70px;
            height: 70px;
            object-fit: contain;
        }

        .element-name {
            display: -webkit-box;
            min-height: 42px;
            overflow: hidden;
            color: var(--vote-ink);
            font-size: 17px;
            font-weight: 900;
            line-height: 1.22;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
        }

        .element-meta {
            max-width: 100%;
            border-radius: 999px;
            background: rgba(21, 24, 39, 0.06);
            color: var(--vote-muted);
            font-size: 12px;
            font-weight: 900;
            padding: 5px 10px;
        }

        .element-check {
            position: absolute;
            top: 12px;
            right: 12px;
            display: grid;
            width: 28px;
            height: 28px;
            place-items: center;
            border-radius: 999px;
            background: #ffffff;
            color: var(--team-color, var(--vote-primary));
            font-size: 15px;
            font-weight: 900;
            opacity: 0;
            transform: scale(0.82);
            transition: opacity 160ms ease, transform 160ms ease;
        }

        .element-card:hover {
            border-color: var(--team-color, var(--vote-primary));
            box-shadow: 0 16px 36px rgba(21, 24, 39, 0.12);
            transform: translateY(-2px);
        }

        .element-input:focus-visible + .element-card {
            border-color: var(--team-color, var(--vote-primary));
            box-shadow: 0 0 0 4px rgba(217, 35, 78, 0.14);
        }

        .element-input:checked + .element-card {
            border-color: var(--team-color, var(--vote-primary));
            box-shadow: 0 18px 38px rgba(21, 24, 39, 0.16);
            transform: translateY(-2px);
        }

        .element-input:checked + .element-card .element-check {
            opacity: 1;
            transform: scale(1);
        }

        @media (max-width: 520px) {
            .element-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
            }

            .element-card {
                min-height: 174px;
                border-radius: 16px;
                padding: 16px 10px;
            }

            .element-logo {
                width: 72px;
                height: 72px;
                border-radius: 16px;
            }

            .element-logo img {
                width: 58px;
                height: 58px;
            }
        }
    </style>
@endsection

@section('content')
    <section class="vote-card">
        <div class="vote-card-inner">
            <span class="vote-kicker">Голосування за команду</span>
            <h1 class="vote-title">{{ $vote->name }}</h1>
            <p class="vote-copy">{{ $user->name }}, оберіть команду, якій хочете віддати голос. Власна команда вже прибрана зі списку.</p>

            @if($errors->any())
                <div class="vote-error" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            @if($alreadyVoted)
                <div class="vote-success" role="status">
                    Ваш голос уже зараховано. Дякуємо за участь!
                </div>
            @elseif($teams->isEmpty())
                <div class="vote-error" role="alert">
                    Немає доступних команд для голосування.
                </div>
            @else
                <form class="vote-form" action="{{ route('votes.submitVote', ['voteUrl' => $vote->vote_url, 'userId' => $user->id]) }}" method="POST">
                    @csrf
                    <div class="element-grid" role="radiogroup" aria-label="Оберіть команду">
                        @foreach($teams as $team)
                            <label class="element-option" style="--team-color: {{ $team->element?->color ?? '#d9234e' }};">
                                <input
                                    class="element-input"
                                    type="radio"
                                    name="team_id"
                                    value="{{ $team->id }}"
                                    @checked((string) old('team_id') === (string) $team->id)
                                    required
                                />
                                <span class="element-card">
                                    <span class="element-check" aria-hidden="true">✓</span>
                                    <span class="element-logo">
                                        <img src="{{ $team->element_logo_url }}" alt="{{ $team->element?->name ?? $team->name }}">
                                    </span>
                                    <span class="element-name">{{ $team->name }}</span>
                                    <span class="element-meta">{{ $team->element?->name ?? 'Команда' }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>

                    <div class="vote-actions">
                        <button id="teamVoteButton" type="submit" class="vote-button" disabled>Проголосувати</button>
                    </div>
                </form>
            @endif
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const button = document.getElementById('teamVoteButton');
            const inputs = document.querySelectorAll('input[name="team_id"]');

            if (!button || inputs.length === 0) {
                return;
            }

            const syncButtonState = function () {
                button.disabled = !Array.from(inputs).some(function (input) {
                    return input.checked;
                });
            };

            inputs.forEach(function (input) {
                input.addEventListener('change', syncButtonState);
            });

            syncButtonState();
        });
    </script>
@endsection
