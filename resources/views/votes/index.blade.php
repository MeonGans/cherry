@extends('layouts.app')

@section('content')
    <div class="panel">
        <div class="mb-5 flex items-center justify-between">
            <h5 class="text-lg font-semibold dark:text-white-light">Список голосувань</h5>
            <a href="{{ route('votes.create') }}" class="btn btn-primary">Створити</a>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Назва</th>
                    <th>Тип</th>
                    <th>URL</th>
                    <th>Фото</th>
                    <th>Результат</th>
                    <th>Бали</th>
                    <th>Керування</th>
                </tr>
                </thead>
                <tbody>
                @forelse($votes as $vote)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $vote->name }}</td>
                        <td>
                            <span class="badge {{ $vote->isPhotoVote() ? 'bg-info' : 'bg-primary' }}">
                                {{ $vote->isPhotoVote() ? 'Фото' : 'Команди' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('votes.show', $vote->vote_url) }}">
                                {{ url('/votes/' . $vote->vote_url) }}
                            </a>
                        </td>
                        <td>{{ $vote->isPhotoVote() ? $vote->photos_count . ' / 10' : '-' }}</td>
                        <td>
                            <a href="{{ route('votes.result', $vote->vote_url) }}" class="btn btn-outline-primary btn-sm">Результат</a>
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
