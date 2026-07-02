@extends('layouts.vote')

@section('title', $vote->name . ' - CHERRY CAMP')

@section('styles')
    <style>
        .vote-shell {
            width: min(100%, 980px);
        }

        .photo-vote-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin-top: 28px;
        }

        .photo-vote-note {
            margin: 0;
            color: var(--vote-muted);
            font-weight: 800;
        }

        .photo-vote-counter {
            display: inline-flex;
            min-width: 78px;
            min-height: 36px;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: var(--vote-ink);
            color: #ffffff;
            font-weight: 900;
            padding: 0 14px;
            white-space: nowrap;
        }

        .photo-vote-list {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            gap: 22px;
            margin-top: 22px;
        }

        .photo-option {
            position: relative;
            min-width: 0;
        }

        .photo-checkbox {
            position: absolute;
            width: 1px;
            height: 1px;
            opacity: 0;
            pointer-events: none;
        }

        .photo-frame {
            position: relative;
            display: grid;
            overflow: hidden;
            border: 1px solid var(--vote-line);
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 10px 26px rgba(21, 24, 39, 0.07);
            cursor: pointer;
            padding: 12px;
            transition: border-color 160ms ease, box-shadow 160ms ease, transform 160ms ease, opacity 160ms ease;
        }

        .photo-image-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-radius: 8px;
            background: #eef1f6;
        }

        .photo-image-wrap img {
            display: block;
            width: auto;
            max-width: 100%;
            height: auto;
            max-height: min(78vh, 820px);
            object-fit: contain;
        }

        .photo-check {
            position: absolute;
            top: 22px;
            right: 22px;
            display: grid;
            width: 38px;
            height: 38px;
            place-items: center;
            border-radius: 999px;
            background: #ffffff;
            color: var(--vote-primary);
            font-size: 18px;
            font-weight: 900;
            opacity: 0;
            transform: scale(0.82);
            transition: opacity 160ms ease, transform 160ms ease;
            z-index: 2;
        }

        .photo-frame:hover {
            border-color: var(--vote-primary);
            box-shadow: 0 16px 34px rgba(21, 24, 39, 0.12);
            transform: translateY(-2px);
        }

        .photo-checkbox:focus-visible + .photo-frame {
            border-color: var(--vote-primary);
            box-shadow: 0 0 0 4px rgba(217, 35, 78, 0.13);
        }

        .photo-checkbox:checked + .photo-frame {
            border-color: var(--vote-primary);
            box-shadow: 0 18px 40px rgba(217, 35, 78, 0.18);
            transform: translateY(-2px);
        }

        .photo-checkbox:checked + .photo-frame .photo-check {
            opacity: 1;
            transform: scale(1);
        }

        .photo-checkbox:disabled + .photo-frame {
            cursor: not-allowed;
            opacity: 0.52;
            transform: none;
        }

        @media (max-width: 640px) {
            .photo-vote-toolbar {
                align-items: flex-start;
                flex-direction: column;
            }

            .photo-vote-list {
                gap: 16px;
            }

            .photo-frame {
                padding: 8px;
            }

            .photo-check {
                top: 16px;
                right: 16px;
            }
        }
    </style>
@endsection

@section('content')
    <section class="vote-card">
        <div class="vote-card-inner">
            <span class="vote-kicker">Фото-голосування</span>
            <h1 class="vote-title">{{ $vote->name }}</h1>
            <p class="vote-copy">{{ $user->name }}, оберіть рівно 3 фотографії, які хочете підтримати.</p>

            @if($errors->any())
                <div class="vote-error" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            @if($alreadyVoted)
                <div class="vote-success" role="status">
                    Ваш голос уже зараховано. Дякуємо за участь!
                </div>
            @elseif($photos->count() < 3)
                <div class="vote-error" role="alert">
                    Для цього голосування ще не завантажено достатньо фото.
                </div>
            @else
                <form id="photoVoteForm" class="vote-form" action="{{ route('votes.submitVote', ['voteUrl' => $vote->vote_url, 'userId' => $user->id]) }}" method="POST">
                    @csrf
                    @php
                        $oldPhotoIds = collect(old('photo_ids', []))->map(fn ($id) => (string) $id)->all();
                    @endphp

                    <div class="photo-vote-toolbar">
                        <p class="photo-vote-note">Обрані фото підсвітяться рамкою. Після третього вибору інші фото тимчасово заблокуються.</p>
                        <span id="selectedCounter" class="photo-vote-counter">0 / 3</span>
                    </div>

                    <div class="photo-vote-list" aria-label="Оберіть 3 фотографії">
                        @foreach($photos as $photo)
                            <label class="photo-option" aria-label="Обрати фото">
                                <input
                                    type="checkbox"
                                    name="photo_ids[]"
                                    value="{{ $photo->id }}"
                                    class="photo-checkbox"
                                    @checked(in_array((string) $photo->id, $oldPhotoIds, true))
                                >
                                <span class="photo-frame">
                                    <span class="photo-image-wrap">
                                        <img src="{{ asset($photo->image_path) }}" alt="Фото для голосування">
                                    </span>
                                    <span class="photo-check" aria-hidden="true">✓</span>
                                </span>
                            </label>
                        @endforeach
                    </div>

                    <div class="vote-actions">
                        <button id="submitVoteBtn" type="submit" class="vote-button" disabled>Проголосувати</button>
                    </div>
                </form>
            @endif
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkboxes = Array.from(document.querySelectorAll('.photo-checkbox'));
            const counter = document.getElementById('selectedCounter');
            const button = document.getElementById('submitVoteBtn');

            if (!counter || !button || checkboxes.length === 0) {
                return;
            }

            const sync = function () {
                const selected = checkboxes.filter(function (checkbox) {
                    return checkbox.checked;
                }).length;

                counter.textContent = selected + ' / 3';
                button.disabled = selected !== 3;

                checkboxes.forEach(function (checkbox) {
                    checkbox.disabled = selected >= 3 && !checkbox.checked;
                });
            };

            checkboxes.forEach(function (checkbox) {
                checkbox.addEventListener('change', sync);
            });

            sync();
        });
    </script>
@endsection
