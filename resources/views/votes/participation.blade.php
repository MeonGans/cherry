@extends('layouts.app')

@section('content')
    @php
        $typeLabel = $vote->isPhotoVote()
            ? 'Фото'
            : ($vote->isOscarVote() ? 'Оскар' : 'Команди');
        $progress = $totalCount > 0 ? round(($votedCount / $totalCount) * 100) : 0;
    @endphp

    <div class="panel">
        <div class="mb-5 flex flex-wrap items-start justify-between gap-3">
            <div>
                <h5 class="text-lg font-semibold dark:text-white-light">Участь у голосуванні</h5>
                <p class="mt-1 text-sm text-white-dark">
                    {{ $vote->name }}
                    <span class="mx-1">•</span>
                    {{ $typeLabel }}
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('votes.result', $vote->vote_url) }}" class="btn btn-outline-primary">Результат</a>
                <a href="{{ route('votes.index') }}" class="btn btn-outline-secondary">До голосувань</a>
            </div>
        </div>

        @if($sessionNote)
            <div class="mb-5 rounded border border-warning bg-warning-light p-3 text-warning">
                {{ $sessionNote }}
            </div>
        @endif

        @if(!$session)
            <div class="rounded border border-danger bg-danger-light p-4 text-danger">
                Неможливо показати список учасників, бо для цього голосування не визначено заїзд.
            </div>
        @else
            <div class="mb-5 rounded border border-white-light p-4 dark:border-[#1b2e4b]">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <div class="text-sm text-white-dark">Заїзд</div>
                        <div class="mt-1 text-base font-semibold text-black dark:text-white">
                            {{ \Illuminate\Support\Carbon::parse($session->start_date)->format('d.m.Y') }}
                            -
                            {{ \Illuminate\Support\Carbon::parse($session->end_date)->format('d.m.Y') }}
                        </div>
                    </div>
                    @if($session->active)
                        <span class="badge bg-success">Активна сесія</span>
                    @else
                        <span class="badge bg-dark">Неактивна сесія</span>
                    @endif
                </div>
            </div>

            <div class="mb-6 grid gap-4 md:grid-cols-4">
                <div class="rounded border border-white-light p-4 dark:border-[#1b2e4b]">
                    <div class="text-xs font-semibold uppercase text-white-dark">Учасників</div>
                    <div class="mt-2 text-2xl font-bold text-black dark:text-white">{{ $totalCount }}</div>
                </div>
                <div class="rounded border border-white-light p-4 dark:border-[#1b2e4b]">
                    <div class="text-xs font-semibold uppercase text-white-dark">Проголосували</div>
                    <div class="mt-2 text-2xl font-bold text-success">{{ $votedCount }}</div>
                </div>
                <div class="rounded border border-white-light p-4 dark:border-[#1b2e4b]">
                    <div class="text-xs font-semibold uppercase text-white-dark">Очікуємо</div>
                    <div class="mt-2 text-2xl font-bold text-warning">{{ $pendingCount }}</div>
                </div>
                <div class="rounded border border-white-light p-4 dark:border-[#1b2e4b]">
                    <div class="text-xs font-semibold uppercase text-white-dark">Готовність</div>
                    <div class="mt-2 text-2xl font-bold text-primary">{{ $progress }}%</div>
                    <div class="mt-3 h-2 overflow-hidden rounded bg-white-light dark:bg-dark">
                        <div class="h-full rounded bg-primary" style="width: {{ $progress }}%;"></div>
                    </div>
                </div>
            </div>

            <div class="mb-5">
                <input
                    type="search"
                    class="form-input max-w-md"
                    placeholder="Пошук учня, команди, PIN або вибору"
                    data-table-search="#vote-participation-table"
                >
            </div>

            <div class="table-responsive">
                <table id="vote-participation-table" class="table-hover table">
                    <thead>
                    <tr>
                        <th class="w-16">#</th>
                        <th>Учасник</th>
                        <th class="w-48">Команда</th>
                        <th class="w-32">PIN</th>
                        <th class="w-44">Статус</th>
                        <th>Вибір</th>
                        <th class="w-40">Час</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($participants as $index => $participant)
                        @php
                            $user = $participant['user'];
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="flex items-center gap-3">
                                    <img
                                        src="{{ $user->image_url }}"
                                        alt="{{ $user->name }}"
                                        class="h-11 w-11 rounded object-cover"
                                    >
                                    <div>
                                        <div class="font-semibold text-black dark:text-white">{{ $user->name }}</div>
                                        <div class="text-xs text-white-dark">ID: {{ $user->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($user->team)
                                    <span class="inline-flex items-center gap-2">
                                        <span
                                            class="h-2.5 w-2.5 rounded-full"
                                            style="background-color: {{ $user->team->element->color ?? '#4361ee' }};"
                                        ></span>
                                        {{ $user->team->name }}
                                    </span>
                                @else
                                    <span class="text-white-dark">Без команди</span>
                                @endif
                            </td>
                            <td>{{ $user->pin_code ?? '—' }}</td>
                            <td>
                                @if($participant['has_voted'])
                                    <span class="badge bg-success">Проголосував</span>
                                @else
                                    <span class="badge bg-warning">Очікуємо</span>
                                @endif
                            </td>
                            <td>
                                @if(!$participant['has_voted'])
                                    <span class="text-white-dark">Ще немає голосу</span>
                                @elseif($participant['kind'] === \App\Models\Vote::TYPE_PHOTO)
                                    <div class="flex flex-wrap gap-3">
                                        @foreach($participant['choices'] as $photo)
                                            <div class="flex items-center gap-2 rounded border border-white-light px-2 py-1 dark:border-[#1b2e4b]">
                                                <img
                                                    src="{{ $photo['image_url'] }}"
                                                    alt="{{ $photo['title'] }}"
                                                    class="h-10 w-10 rounded object-cover"
                                                >
                                                <span class="text-sm">{{ $photo['title'] }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif($participant['kind'] === \App\Models\Vote::TYPE_OSCAR)
                                    <div class="grid gap-2">
                                        @foreach($participant['choices'] as $choice)
                                            <div>
                                                <span class="font-semibold text-black dark:text-white">{{ $choice['title'] }}:</span>
                                                <span>{{ $choice['nominees']->implode(', ') }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    @foreach($participant['choices'] as $choice)
                                        <span class="inline-flex items-center gap-2 rounded border border-white-light px-3 py-1 dark:border-[#1b2e4b]">
                                            <span
                                                class="h-2.5 w-2.5 rounded-full"
                                                style="background-color: {{ $choice['color'] }};"
                                            ></span>
                                            {{ $choice['value'] }}
                                        </span>
                                    @endforeach
                                @endif
                            </td>
                            <td>
                                @if($participant['voted_at'])
                                    {{ \Illuminate\Support\Carbon::parse($participant['voted_at'])->format('d.m.Y H:i') }}
                                @else
                                    <span class="text-white-dark">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr data-empty-row>
                            <td colspan="7" class="text-center text-white-dark">У цьому заїзді ще немає учасників.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
