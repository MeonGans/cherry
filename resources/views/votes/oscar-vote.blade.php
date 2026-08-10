@extends('layouts.vote')

@section('title', $vote->name . ' - CHERRY CAMP')

@section('styles')
    <style>
        .vote-shell {
            width: min(100%, 1180px);
        }

        .oscar-hero {
            display: grid;
            gap: 10px;
            margin-bottom: 30px;
            text-align: center;
        }

        .oscar-card {
            border: 1px solid rgba(234, 216, 166, 0.9);
            background:
                linear-gradient(135deg, rgba(212, 175, 55, 0.12), transparent 36%),
                rgba(255, 255, 255, 0.96);
        }

        .oscar-kicker {
            background: rgba(212, 175, 55, 0.14);
            color: #9a6a00;
        }

        .oscar-copy {
            margin-inline: auto;
        }

        .oscar-nominations {
            display: grid;
            gap: 18px;
            margin-top: 28px;
        }

        .oscar-nomination {
            overflow: hidden;
            border: 1px solid #ead8a6;
            border-radius: 18px;
            background: #fffdf7;
            box-shadow: 0 14px 34px rgba(120, 78, 0, 0.08);
        }

        .nomination-title-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            border-bottom: 1px solid rgba(234, 216, 166, 0.7);
            padding: 18px;
        }

        .nomination-kicker {
            margin: 0 0 4px;
            color: #9a6a00;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .nomination-title-row h2 {
            margin: 0;
            color: var(--vote-ink);
            font-size: clamp(20px, 4vw, 28px);
            font-weight: 900;
            line-height: 1.15;
        }

        .selection-counter {
            display: inline-flex;
            min-width: 76px;
            min-height: 36px;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: var(--vote-ink);
            color: #ffffff;
            font-weight: 900;
            padding: 0 12px;
        }

        .nominee-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 14px;
            padding: 18px;
        }

        .nominee-option {
            position: relative;
            min-width: 0;
        }

        .nominee-input {
            position: absolute;
            width: 1px;
            height: 1px;
            opacity: 0;
            pointer-events: none;
        }

        .nominee-card {
            position: relative;
            display: grid;
            overflow: hidden;
            min-height: 226px;
            border: 1px solid #ead8a6;
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 10px 22px rgba(120, 78, 0, 0.08);
            cursor: pointer;
            transition: border-color 160ms ease, box-shadow 160ms ease, transform 160ms ease, opacity 160ms ease;
        }

        .nominee-card img {
            display: block;
            width: 100%;
            aspect-ratio: 1 / 1;
            background: #f6f0dc;
            object-fit: cover;
        }

        .nominee-name {
            display: flex;
            min-height: 58px;
            align-items: center;
            justify-content: center;
            color: var(--vote-ink);
            font-weight: 900;
            line-height: 1.25;
            padding: 10px;
            text-align: center;
        }

        .nominee-check {
            position: absolute;
            top: 10px;
            right: 10px;
            display: grid;
            width: 30px;
            height: 30px;
            place-items: center;
            border-radius: 999px;
            background: #ffffff;
            color: #9a6a00;
            font-size: 15px;
            font-weight: 900;
            opacity: 0;
            transform: scale(0.82);
            transition: opacity 160ms ease, transform 160ms ease;
        }

        .nominee-card:hover {
            border-color: #d4af37;
            box-shadow: 0 16px 34px rgba(120, 78, 0, 0.14);
            transform: translateY(-2px);
        }

        .nominee-input:focus-visible + .nominee-card {
            border-color: #d4af37;
            box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.22);
        }

        .nominee-input:checked + .nominee-card {
            border-color: #d4af37;
            box-shadow: 0 18px 38px rgba(212, 175, 55, 0.2);
            transform: translateY(-2px);
        }

        .nominee-input:checked + .nominee-card .nominee-check {
            opacity: 1;
            transform: scale(1);
        }

        .nominee-input:disabled + .nominee-card {
            cursor: not-allowed;
            opacity: 0.5;
            transform: none;
        }

        .empty-nomination {
            margin: 18px;
            border-radius: 14px;
            background: #fff6df;
            color: #8a5b00;
            font-weight: 800;
            padding: 14px 16px;
        }

        .oscar-submit {
            max-width: 420px;
            margin-inline: auto;
        }

        @media (max-width: 640px) {
            .oscar-hero {
                text-align: left;
            }

            .nomination-title-row {
                flex-direction: column;
            }

            .nominee-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
                padding: 14px;
            }

            .nominee-card {
                min-height: 196px;
            }

            .nominee-name {
                min-height: 52px;
                font-size: 13px;
                padding: 8px;
            }
        }
    </style>
@endsection

@section('content')
    <section class="vote-card oscar-card">
        <div class="vote-card-inner">
            <div class="oscar-hero">
                <span class="vote-kicker oscar-kicker">Cherry Camp Awards</span>
                <h1 class="vote-title">{{ $vote->name }}</h1>
                <p class="vote-copy oscar-copy">{{ $user->name }}, оберіть номінантів у кожній категорії. Кнопка активується, коли всі номінації заповнені.</p>
            </div>

            @if($errors->any())
                <div class="vote-error" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            @if($alreadyVoted)
                <div class="vote-success" role="status">
                    Ваш голос уже зараховано. Дякуємо за участь!
                </div>
            @else
                <form id="oscarVoteForm" class="vote-form" action="{{ route('votes.submitVote', ['voteUrl' => $vote->vote_url, 'userId' => $user->id]) }}" method="POST">
                    @csrf

                    <div class="oscar-nominations">
                        @foreach($nominations as $key => $nomination)
                            @php
                                $limit = $oscarSelectionLimits[$key] ?? $nomination['limit'];
                                $type = $limit === 1 ? 'radio' : 'checkbox';
                                $candidates = $candidatesByNomination[$key] ?? collect();
                                $oldSelected = collect(old("oscar_votes.{$key}", []))->map(fn ($id) => (string) $id)->all();
                            @endphp

                            <section class="oscar-nomination" data-limit="{{ $limit }}" data-key="{{ $key }}">
                                <div class="nomination-title-row">
                                    <div>
                                        <p class="nomination-kicker">Номінація</p>
                                        <h2>{{ $nomination['title'] }}</h2>
                                    </div>
                                    <span class="selection-counter">0 / {{ $limit }}</span>
                                </div>

                                @if($candidates->isEmpty())
                                    <div class="empty-nomination">У цій номінації немає кандидатів.</div>
                                @else
                                    <div class="nominee-grid">
                                        @foreach($candidates as $candidate)
                                            <label class="nominee-option">
                                                <input
                                                    type="{{ $type }}"
                                                    name="oscar_votes[{{ $key }}][]"
                                                    value="{{ $candidate->id }}"
                                                    class="nominee-input"
                                                    @checked(in_array((string) $candidate->id, $oldSelected, true))
                                                >
                                                <span class="nominee-card">
                                                    <img src="{{ $candidate->image_url }}" alt="{{ $candidate->name }}">
                                                    <span class="nominee-check" aria-hidden="true">✓</span>
                                                    <span class="nominee-name">{{ $candidate->name }}</span>
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                @endif
                            </section>
                        @endforeach
                    </div>

                    <div class="vote-actions oscar-submit">
                        <button id="submitOscarVote" type="submit" class="vote-button" disabled>Проголосувати</button>
                    </div>
                </form>
            @endif
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sections = Array.from(document.querySelectorAll('.oscar-nomination'));
            const submitButton = document.getElementById('submitOscarVote');

            if (!submitButton || sections.length === 0) {
                return;
            }

            const syncSection = function (section) {
                const limit = Number(section.dataset.limit);
                const inputs = Array.from(section.querySelectorAll('.nominee-input'));
                const checked = inputs.filter(function (input) {
                    return input.checked;
                }).length;
                const counter = section.querySelector('.selection-counter');

                if (counter) {
                    counter.textContent = checked + ' / ' + limit;
                }

                if (inputs.length === 0) {
                    return false;
                }

                if (limit > 1) {
                    inputs.forEach(function (input) {
                        input.disabled = checked >= limit && !input.checked;
                    });
                }

                return checked === limit;
            };

            const syncForm = function () {
                const complete = sections.every(syncSection);
                submitButton.disabled = !complete;
            };

            sections.forEach(function (section) {
                section.querySelectorAll('.nominee-input').forEach(function (input) {
                    input.addEventListener('change', syncForm);
                });
            });

            syncForm();
        });
    </script>
@endsection
