@extends('layouts.app')

@section('content')
    <div class="panel">
        <div class="mb-5 flex items-center justify-between">
            <h5 class="text-lg font-semibold dark:text-white-light">Список голосувань</h5>
            <a href="{{ route('votes.create') }}" class="btn btn-primary">Створити</a>
        </div>

        <div class="mb-5">
            <input
                type="search"
                class="form-input max-w-md"
                placeholder="Пошук за назвою, типом або URL"
                data-table-search="#votes-table"
            >
        </div>

        <div class="table-responsive">
            <table id="votes-table" class="table-hover table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Назва</th>
                    <th>Тип</th>
                    <th>URL</th>
                    <th>Дані</th>
                    <th>Результат</th>
                    <th>Бали</th>
                    <th>Керування</th>
                </tr>
                </thead>
                <tbody>
                @forelse($votes as $vote)
                    <tr data-empty-row>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $vote->name }}</td>
                        <td>
                            @if($vote->isPhotoVote())
                                <span class="badge bg-info">Фото</span>
                            @elseif($vote->isOscarVote())
                                <span class="badge bg-warning">Оскар</span>
                            @else
                                <span class="badge bg-primary">Команди</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('votes.show', $vote->vote_url) }}">
                                {{ url('/votes/' . $vote->vote_url) }}
                            </a>
                        </td>
                        <td>
                            @if($vote->session)
                                <div class="font-semibold text-black dark:text-white">
                                    Заїзд #{{ $vote->session->id }}
                                </div>
                                <div class="text-xs text-white-dark">
                                    {{ \Illuminate\Support\Carbon::parse($vote->session->start_date)->format('d.m.Y') }}
                                    -
                                    {{ \Illuminate\Support\Carbon::parse($vote->session->end_date)->format('d.m.Y') }}
                                </div>
                            @endif

                            @if($vote->isPhotoVote())
                                <div class="{{ $vote->session ? 'mt-1' : '' }}">{{ $vote->photos_count }} / 10 фото</div>
                            @elseif($vote->isOscarVote())
                                @unless($vote->session)
                                    Сесія #{{ $vote->session_id ?? '-' }}
                                @endunless
                            @else
                                @unless($vote->session)
                                    -
                                @endunless
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('votes.result', $vote->vote_url) }}" class="btn btn-outline-primary btn-sm">Результат</a>
                            <a href="{{ route('votes.result', ['voteUrl' => $vote->vote_url, 'scores' => 1]) }}" class="btn btn-outline-secondary btn-sm">З балами</a>
                            <a href="{{ route('votes.participation', $vote->vote_url) }}" class="btn btn-outline-info btn-sm">Учасники</a>
                        </td>
                        <td>
                            <a href="{{ route('votes.addPointsForm', $vote->vote_url) }}" class="btn btn-outline-warning btn-sm">Додати бали</a>
                        </td>
                        <td>
                            @if($vote->isPhotoVote())
                                <a href="{{ route('votes.photosForm', $vote->vote_url) }}" class="btn btn-outline-info btn-sm">Фото</a>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Голосувань ще немає.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
