@extends('layouts.users.app')

@section('content')
    <div class="panel">
        <div class="mb-5 flex items-center justify-between">
            <h5 class="text-lg font-semibold dark:text-white-light">{{ $vote->name }}</h5>
        </div>

        @if($errors->any())
            <div class="mb-4 rounded border border-danger bg-danger/10 p-3 text-danger">
                {{ $errors->first() }}
            </div>
        @endif

        @if($alreadyVoted)
            <div class="rounded border border-success bg-success/10 p-4 font-semibold text-success">
                Ваш голос уже зараховано.
            </div>
        @elseif($photos->count() < 3)
            <div class="rounded border border-warning bg-warning/10 p-4 font-semibold text-warning">
                Для цього голосування ще не завантажено достатньо фото.
            </div>
        @else
            <form id="photoVoteForm" action="{{ route('votes.submitVote', ['voteUrl' => $vote->vote_url, 'userId' => $user->id]) }}" method="POST">
                @csrf

                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                    <p class="text-base font-semibold">Оберіть 3 фотографії</p>
                    <p id="selectedCounter" class="rounded bg-primary px-3 py-1 text-sm font-bold text-white">0 / 3</p>
                </div>

                <div class="photo-grid">
                    @foreach($photos as $photo)
                        <label class="photo-option">
                            <input type="checkbox" name="photo_ids[]" value="{{ $photo->id }}" class="photo-checkbox">
                            <span class="photo-frame">
                                <img src="{{ asset($photo->image_path) }}" alt="{{ $photo->title ?? 'Фото ' . $loop->iteration }}">
                                <span class="photo-title">{{ $photo->title ?? 'Фото ' . $loop->iteration }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>

                <button id="submitVoteBtn" type="submit" class="btn btn-primary mt-6" disabled>Проголосувати</button>
            </form>
        @endif
    </div>
@endsection

@section('styles')
    <style>
        .photo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 16px;
        }

        .photo-option {
            cursor: pointer;
            display: block;
        }

        .photo-option input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .photo-frame {
            display: flex;
            min-height: 240px;
            flex-direction: column;
            overflow: hidden;
            border: 2px solid #e0e6ed;
            border-radius: 8px;
            background: #fff;
            transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
        }

        .photo-frame img {
            display: block;
            width: 100%;
            height: auto;
            background: #f1f2f3;
        }

        .photo-title {
            display: block;
            padding: 10px 12px;
            font-weight: 700;
            text-align: center;
        }

        .photo-option input:checked + .photo-frame {
            border-color: #4361ee;
            box-shadow: 0 12px 24px rgba(67, 97, 238, .18);
            transform: translateY(-2px);
        }

        .photo-option input:disabled + .photo-frame {
            cursor: not-allowed;
            opacity: .55;
        }
    </style>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const checkboxes = Array.from(document.querySelectorAll('.photo-checkbox'));
            const counter = document.getElementById('selectedCounter');
            const button = document.getElementById('submitVoteBtn');

            const sync = () => {
                const selected = checkboxes.filter((checkbox) => checkbox.checked).length;
                counter.textContent = `${selected} / 3`;
                button.disabled = selected !== 3;

                checkboxes.forEach((checkbox) => {
                    checkbox.disabled = selected >= 3 && !checkbox.checked;
                });
            };

            checkboxes.forEach((checkbox) => checkbox.addEventListener('change', sync));
            sync();
        });
    </script>
@endsection
