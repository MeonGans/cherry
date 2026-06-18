@extends('layouts.app')

@section('content')
    <div class="panel">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h5 class="text-lg font-semibold dark:text-white-light">Картки кліпу</h5>
                <p class="text-sm text-white-dark">Керуйте жанрами та кліпами для сторінки “музикальний кліп”.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('music-clip-cards.create', ['type' => \App\Models\MusicClipCard::TYPE_GENRE]) }}" class="btn btn-primary">Додати жанр</a>
                <a href="{{ route('music-clip-cards.create', ['type' => \App\Models\MusicClipCard::TYPE_SONG]) }}" class="btn btn-outline-primary">Додати кліп</a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-5 rounded border border-success bg-success-light p-3 text-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-5 rounded border border-danger bg-danger-light p-3 text-danger">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @foreach($genres->merge($songs) as $card)
            <form id="quick-update-clip-card-{{ $card->id }}" action="{{ route('music-clip-cards.quick-update', $card) }}" method="POST" class="hidden">
                @csrf
                @method('PATCH')
            </form>
        @endforeach

        @php
            $groups = [
                [
                    'title' => 'Жанри',
                    'caption' => 'Верхня стрічка. Крутиться справа наліво.',
                    'cards' => $genres,
                    'type' => \App\Models\MusicClipCard::TYPE_GENRE,
                    'total' => $totals[\App\Models\MusicClipCard::TYPE_GENRE],
                ],
                [
                    'title' => 'Кліпи',
                    'caption' => 'Нижня стрічка. Крутиться зліва направо.',
                    'cards' => $songs,
                    'type' => \App\Models\MusicClipCard::TYPE_SONG,
                    'total' => $totals[\App\Models\MusicClipCard::TYPE_SONG],
                ],
            ];
        @endphp

        <div class="grid gap-6">
            @foreach($groups as $group)
                <section class="rounded border border-white-light p-4 dark:border-[#1b2e4b]">
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h6 class="text-base font-semibold text-black dark:text-white">{{ $group['title'] }}</h6>
                            <p class="text-sm text-white-dark">{{ $group['caption'] }}</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <div class="rounded bg-primary-light px-3 py-2 text-primary">
                                Всього: <span class="font-semibold">{{ $group['total'] }}</span>
                            </div>
                            <a href="{{ route('music-clip-cards.create', ['type' => $group['type']]) }}" class="btn btn-primary btn-sm">
                                Додати
                            </a>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table-hover table">
                            <thead>
                            <tr>
                                <th class="w-20">#</th>
                                <th>Картка</th>
                                <th class="w-40">Шанс появи</th>
                                <th class="w-64 text-right">Дії</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($group['cards'] as $card)
                                <tr>
                                    <td>{{ $card->id }}</td>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <img
                                                src="{{ $card->image_url }}"
                                                alt="{{ $card->name }}"
                                                class="h-12 w-12 rounded object-cover"
                                            >
                                            <div>
                                                <div class="font-semibold text-black dark:text-white">{{ $card->name }}</div>
                                                <div class="text-xs text-white-dark">{{ $card->type_label }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <input
                                            type="number"
                                            name="quantity"
                                            min="0"
                                            step="1"
                                            value="{{ $card->quantity }}"
                                            form="quick-update-clip-card-{{ $card->id }}"
                                            class="form-input w-28"
                                            required
                                        >
                                    </td>
                                    <td>
                                        <div class="flex flex-wrap justify-end gap-2">
                                            <button type="submit" form="quick-update-clip-card-{{ $card->id }}" class="btn btn-primary btn-sm">
                                                Зберегти
                                            </button>
                                            <a href="{{ route('music-clip-cards.edit', $card) }}" class="btn btn-outline-primary btn-sm">
                                                Редагувати
                                            </a>
                                            <form action="{{ route('music-clip-cards.destroy', $card) }}" method="POST" onsubmit="return confirm('Видалити цю картку?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm">Видалити</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-white-dark">Карток ще немає.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            @endforeach
        </div>
    </div>
@endsection
