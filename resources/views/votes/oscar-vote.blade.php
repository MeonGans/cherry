@extends('layouts.users.app')

@section('content')
    <div class="oscar-shell">
        <div class="oscar-header">
            <p>Cherry Camp Awards</p>
            <h1>{{ $vote->name }}</h1>
        </div>

        @if($errors->any())
            <div class="mb-4 rounded border border-danger bg-danger/10 p-3 text-danger">
                {{ $errors->first() }}
            </div>
        @endif

        @if($alreadyVoted)
            <div class="panel text-center">
                <h2 class="text-xl font-black">Ваш голос уже зараховано.</h2>
            </div>
        @else
            <form id="oscarVoteForm" action="{{ route('votes.submitVote', ['voteUrl' => $vote->vote_url, 'userId' => $user->id]) }}" method="POST">
                @csrf

                @foreach($nominations as $key => $nomination)
                    @php
                        $limit = $nomination['limit'];
                        $type = $limit === 1 ? 'radio' : 'checkbox';
                        $candidates = $candidatesByNomination[$key] ?? collect();
                        $oldSelected = old("oscar_votes.{$key}", []);
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
                                            @checked(in_array($candidate->id, $oldSelected))
                                        >
                                        <span class="nominee-card">
                                            <img src="{{ $candidate->image_url }}" alt="{{ $candidate->name }}">
                                            <span class="nominee-name">{{ $candidate->name }}</span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                    </section>
                @endforeach

                <button id="submitOscarVote" type="submit" class="btn btn-primary mx-auto mt-8 block" disabled>Проголосувати</button>
            </form>
        @endif
    </div>
@endsection

@section('styles')
    <style>
        .oscar-shell {
            max-width: 1180px;
            margin: 0 auto;
        }

        .oscar-header {
            margin-bottom: 28px;
            text-align: center;
        }

        .oscar-header p {
            margin-bottom: 8px;
            color: #b8860b;
            font-size: .9rem;
            font-weight: 900;
            text-transform: uppercase;
        }

        .oscar-header h1 {
            color: #111827;
            font-size: 2.25rem;
            font-weight: 900;
        }

        .oscar-nomination {
            margin-bottom: 24px;
            border: 1px solid #ead8a6;
            border-radius: 8px;
            background: #fffdf7;
            padding: 18px;
            box-shadow: 0 12px 30px rgba(120, 78, 0, .08);
        }

        .nomination-title-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 16px;
        }

        .nomination-kicker {
            margin-bottom: 4px;
            color: #9a6a00;
            font-size: .78rem;
            font-weight: 900;
            text-transform: uppercase;
        }

        .nomination-title-row h2 {
            color: #111827;
            font-size: 1.4rem;
            font-weight: 900;
        }

        .selection-counter {
            min-width: 72px;
            border-radius: 999px;
            background: #111827;
            padding: 7px 12px;
            color: #fff;
            font-weight: 900;
            text-align: center;
        }

        .nominee-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 14px;
        }

        .nominee-option {
            display: block;
            cursor: pointer;
        }

        .nominee-option input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .nominee-card {
            display: flex;
            min-height: 225px;
            flex-direction: column;
            overflow: hidden;
            border: 2px solid #ead8a6;
            border-radius: 8px;
            background: #fff;
            transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
        }

        .nominee-card img {
            width: 100%;
            aspect-ratio: 1 / 1;
            object-fit: cover;
            background: #f6f0dc;
        }

        .nominee-name {
            display: flex;
            flex: 1;
            align-items: center;
            justify-content: center;
            padding: 10px;
            color: #111827;
            font-weight: 900;
            text-align: center;
        }

        .nominee-option input:checked + .nominee-card {
            border-color: #d4af37;
            box-shadow: 0 16px 32px rgba(212, 175, 55, .24);
            transform: translateY(-2px);
        }

        .nominee-option input:disabled + .nominee-card {
            cursor: not-allowed;
            opacity: .5;
        }

        .empty-nomination {
            border-radius: 8px;
            background: #fff6df;
            padding: 14px;
            color: #8a5b00;
            font-weight: 800;
        }

        @media (max-width: 640px) {
            .nomination-title-row {
                align-items: flex-start;
                flex-direction: column;
            }

            .oscar-header h1 {
                font-size: 1.8rem;
            }
        }
    </style>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sections = Array.from(document.querySelectorAll('.oscar-nomination'));
            const submitButton = document.getElementById('submitOscarVote');

            const syncSection = (section) => {
                const limit = Number(section.dataset.limit);
                const inputs = Array.from(section.querySelectorAll('.nominee-input'));
                const checked = inputs.filter((input) => input.checked).length;
                const counter = section.querySelector('.selection-counter');

                counter.textContent = `${checked} / ${limit}`;

                if (limit > 1) {
                    inputs.forEach((input) => {
                        input.disabled = checked >= limit && !input.checked;
                    });
                }

                return checked === limit;
            };

            const syncForm = () => {
                const complete = sections.every(syncSection);
                submitButton.disabled = !complete;
            };

            sections.forEach((section) => {
                section.querySelectorAll('.nominee-input').forEach((input) => {
                    input.addEventListener('change', syncForm);
                });
            });

            syncForm();
        });
    </script>
@endsection
