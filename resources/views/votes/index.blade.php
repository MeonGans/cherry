@extends('layouts.app')

@push('styles')
    <style>
        .vote-actions-cell {
            min-width: 292px;
        }

        .vote-actions-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(128px, 1fr));
            gap: 8px;
        }

        .vote-actions-grid .btn,
        .vote-actions-grid form,
        .vote-actions-grid form .btn {
            width: 100%;
            margin: 0;
        }

        .vote-actions-grid .btn {
            min-height: 34px;
            justify-content: center;
            white-space: nowrap;
        }

        .vote-actions-grid .vote-action-wide {
            grid-column: 1 / -1;
        }

        @media (max-width: 640px) {
            .vote-actions-cell {
                min-width: 260px;
            }

            .vote-actions-grid {
                grid-template-columns: repeat(2, minmax(116px, 1fr));
            }
        }
    </style>
@endpush

@section('content')
    <div class="panel">
        <div class="mb-5 flex items-center justify-between">
            <h5 class="text-lg font-semibold dark:text-white-light">Список голосувань</h5>
            <a href="{{ route('votes.create') }}" class="btn btn-primary">Створити</a>
        </div>
        @if(session('success'))
            <div class="mb-4 rounded border border-success bg-success/10 p-3 text-success">{{ session('success') }}</div>
        @endif
        <div class="mb-5">
            <input type="search" class="form-input max-w-md" placeholder="Пошук за назвою, типом або URL" data-table-search="#votes-table">
        </div>
        <div class="table-responsive">
            <table id="votes-table" class="table-hover table">
                <thead><tr><th>#</th><th>Назва</th><th>Тип</th><th>Статус</th><th>URL</th><th>Дані</th><th>Дії</th></tr></thead>
                <tbody>
                @forelse($votes as $vote)
                    <tr data-empty-row>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $vote->name }}</td>
                        <td>
                            @if($vote->isPhotoVote()) <span class="badge bg-info">Фото</span>
                            @elseif($vote->isOscarVote()) <span class="badge bg-warning">Оскар</span>
                            @else <span class="badge bg-primary">Команди</span> @endif
                        </td>
                        <td>
                            @if($vote->isStopped())
                                <span class="badge bg-danger">Зупинено</span>
                                <div class="mt-1 text-xs text-white-dark">{{ $vote->stopped_at->format('d.m.Y H:i') }}</div>
                            @else
                                <span class="badge bg-success">Триває</span>
                            @endif
                        </td>
                        <td><a href="{{ route('votes.show', $vote->vote_url) }}">{{ url('/votes/' . $vote->vote_url) }}</a></td>
                        <td>
                            @if($vote->session)
                                <div class="font-semibold text-black dark:text-white">Заїзд #{{ $vote->session->id }}</div>
                                <div class="text-xs text-white-dark">{{ \Illuminate\Support\Carbon::parse($vote->session->start_date)->format('d.m.Y') }} — {{ \Illuminate\Support\Carbon::parse($vote->session->end_date)->format('d.m.Y') }}</div>
                            @endif
                            @if($vote->isPhotoVote())
                                <div class="mt-1">Фінал: {{ $vote->finalist_photos_count ?? 0 }} / 10 <span class="text-xs text-white-dark">(подано: {{ $vote->photos_count }})</span></div>
                            @elseif(!$vote->session) — @endif
                        </td>
                        <td class="vote-actions-cell">
                            <div class="vote-actions-grid">
                                <a href="{{ route('votes.result', $vote->vote_url) }}" class="btn btn-primary btn-sm">Результат</a>
                                <a href="{{ route('votes.participation', $vote->vote_url) }}" class="btn btn-outline-primary btn-sm">Учасники</a>
                                <a href="{{ route('votes.result', ['voteUrl' => $vote->vote_url, 'scores' => 1]) }}" class="btn btn-outline-secondary btn-sm">З балами</a>
                                <a href="{{ route('votes.addPointsForm', $vote->vote_url) }}" class="btn btn-outline-secondary btn-sm">Додати бали</a>

                                @if($vote->isPhotoVote())
                                    <a href="{{ route('votes.photosForm', $vote->vote_url) }}" class="btn btn-outline-info btn-sm">Керувати фото</a>
                                @elseif($vote->isOscarVote())
                                    <a href="{{ route('votes.oscar.edit', $vote->vote_url) }}" class="btn btn-outline-info btn-sm">Номінанти</a>
                                @endif

                                @unless($vote->isStopped())
                                    <form
                                        action="{{ route('votes.stop', $vote->vote_url) }}"
                                        method="POST"
                                        class="{{ !$vote->isPhotoVote() && !$vote->isOscarVote() ? 'vote-action-wide' : '' }}"
                                        onsubmit="return confirm('Зупинити голосування? Нові голоси більше не прийматимуться.');"
                                    >
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">Зупинити</button>
                                    </form>
                                @endunless
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center">Голосувань ще немає.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
