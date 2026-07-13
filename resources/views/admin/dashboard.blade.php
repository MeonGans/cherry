@extends('layouts.app')

@section('page-title', 'Огляд')

@section('content')
    <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-black dark:text-white">Панель керування</h1>
            <p class="mt-1 text-sm text-white-dark">Швидкий стан заїзду, ключові дії та переходи до основних розділів.</p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('sessions.create') }}" class="btn btn-primary">Створити сесію</a>
            <a href="{{ route('list') }}" class="btn btn-outline-primary">Список заїзду</a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-5 rounded border border-success bg-success-light p-3 text-success">
            {{ session('success') }}
        </div>
    @endif

    @if($databaseUnavailable)
        <div class="mb-5 rounded border border-warning bg-warning-light p-4 text-warning">
            Не вдалося підключитися до бази даних. Навігація доступна, але статистика та списки з'являться після відновлення підключення.
        </div>
    @endif

    <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-6">
        @foreach([
            ['label' => 'Учнів в активній сесії', 'value' => $stats['activeStudents']],
            ['label' => 'Сесій', 'value' => $stats['sessions']],
            ['label' => 'Товарів', 'value' => $stats['products']],
            ['label' => 'Голосувань', 'value' => $stats['votes']],
            ['label' => 'Карток кліпу', 'value' => $stats['clipCards']],
            ['label' => 'Маршрутів квестів', 'value' => $stats['questRoutes']],
        ] as $stat)
            <div class="rounded border border-white-light bg-white p-4 dark:border-[#1b2e4b] dark:bg-[#0e1726]">
                <div class="text-xs font-semibold uppercase text-white-dark">{{ $stat['label'] }}</div>
                <div class="mt-2 text-2xl font-bold text-black dark:text-white">{{ $stat['value'] ?? '—' }}</div>
            </div>
        @endforeach
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <div class="grid gap-6">
            <section class="panel">
                <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold dark:text-white-light">Активний заїзд</h2>
                        <p class="text-sm text-white-dark">Основна робоча точка для списків, друку та додавання учнів.</p>
                    </div>

                    <a href="{{ route('sessions.index') }}" class="btn btn-outline-primary btn-sm">Усі сесії</a>
                </div>

                @if($activeSession)
                    <div class="mb-5 rounded border border-white-light p-4 dark:border-[#1b2e4b]">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <div class="text-xl font-bold text-black dark:text-white">
                                    {{ \Illuminate\Support\Carbon::parse($activeSession->start_date)->format('d.m.Y') }}
                                    -
                                    {{ \Illuminate\Support\Carbon::parse($activeSession->end_date)->format('d.m.Y') }}
                                </div>
                                <div class="mt-1 text-sm text-white-dark">
                                    {{ $activeSession->users_count }} учнів у сесії
                                </div>
                            </div>
                            <span class="badge bg-success">Активна</span>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('sessions.users', $activeSession) }}" class="btn btn-primary">Керувати учнями</a>
                        <a href="{{ route('list') }}" class="btn btn-outline-primary">Список з PIN</a>
                        <a href="{{ route('random_list') }}" class="btn btn-outline-primary">Рандомний список</a>
                    </div>
                @else
                    <div class="rounded border border-white-light p-5 text-center dark:border-[#1b2e4b]">
                        <div class="text-base font-semibold text-black dark:text-white">Активної сесії немає</div>
                        <p class="mx-auto mt-2 max-w-xl text-sm text-white-dark">Створіть або активуйте сесію, щоб список заїзду, друк і голосування працювали від актуального набору учнів.</p>
                        <a href="{{ route('sessions.create') }}" class="btn btn-primary mt-4">Створити сесію</a>
                    </div>
                @endif
            </section>

            <section class="panel">
                <div class="mb-5">
                    <h2 class="text-lg font-semibold dark:text-white-light">Швидкі дії</h2>
                    <p class="text-sm text-white-dark">Найчастіші переходи без пошуку в меню.</p>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    <a href="{{ route('cherries.index') }}" class="rounded border border-white-light p-4 transition hover:border-danger hover:text-danger dark:border-[#1b2e4b]">
                        <div class="font-semibold">Облік Черіків</div>
                        <div class="mt-1 text-xs text-white-dark">Учасники, команди та оголошення переможців</div>
                    </a>
                    <a href="{{ route('votes.create') }}" class="rounded border border-white-light p-4 transition hover:border-primary hover:text-primary dark:border-[#1b2e4b]">
                        <div class="font-semibold">Створити голосування</div>
                        <div class="mt-1 text-xs text-white-dark">Командне, фото або Оскар</div>
                    </a>
                    <a href="{{ route('products.index') }}" class="rounded border border-white-light p-4 transition hover:border-primary hover:text-primary dark:border-[#1b2e4b]">
                        <div class="font-semibold">Товари колеса</div>
                        <div class="mt-1 text-xs text-white-dark">Кількість, цінність, фото</div>
                    </a>
                    <a href="{{ route('music-clip-cards.index') }}" class="rounded border border-white-light p-4 transition hover:border-primary hover:text-primary dark:border-[#1b2e4b]">
                        <div class="font-semibold">Картки кліпу</div>
                        <div class="mt-1 text-xs text-white-dark">Жанри, кліпи та шанси</div>
                    </a>
                    <a href="{{ route('wednesday-quest-routes.index') }}" class="rounded border border-white-light p-4 transition hover:border-primary hover:text-primary dark:border-[#1b2e4b]">
                        <div class="font-semibold">Wednesday маршрути</div>
                        <div class="mt-1 text-xs text-white-dark">Коди та підказки</div>
                    </a>
                    <a href="{{ route('zootopia-quest-routes.index') }}" class="rounded border border-white-light p-4 transition hover:border-primary hover:text-primary dark:border-[#1b2e4b]">
                        <div class="font-semibold">Zootopia маршрути</div>
                        <div class="mt-1 text-xs text-white-dark">Агенти та підказки</div>
                    </a>
                    <a href="{{ route('test.show') }}" class="rounded border border-white-light p-4 transition hover:border-primary hover:text-primary dark:border-[#1b2e4b]">
                        <div class="font-semibold">Сортування</div>
                        <div class="mt-1 text-xs text-white-dark">Командні інструменти</div>
                    </a>
                </div>
            </section>
        </div>

        <div class="grid gap-6">
            <section class="panel">
                <div class="mb-5 flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold dark:text-white-light">Останні сесії</h2>
                        <p class="text-sm text-white-dark">Швидка перевірка заїздів.</p>
                    </div>
                    <a href="{{ route('sessions.index') }}" class="btn btn-outline-primary btn-sm">Перейти</a>
                </div>

                <div class="space-y-3">
                    @forelse($recentSessions as $session)
                        <div class="flex items-center justify-between gap-3 rounded border border-white-light p-3 dark:border-[#1b2e4b]">
                            <div>
                                <div class="font-semibold text-black dark:text-white">
                                    {{ \Illuminate\Support\Carbon::parse($session->start_date)->format('d.m.Y') }}
                                    -
                                    {{ \Illuminate\Support\Carbon::parse($session->end_date)->format('d.m.Y') }}
                                </div>
                                <div class="text-xs text-white-dark">{{ $session->users_count }} учнів</div>
                            </div>
                            <span class="badge {{ $session->active ? 'bg-success' : 'bg-dark' }}">{{ $session->active ? 'Активна' : 'Неактивна' }}</span>
                        </div>
                    @empty
                        <div class="rounded border border-white-light p-4 text-center text-white-dark dark:border-[#1b2e4b]">
                            Сесій ще немає.
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="panel">
                <div class="mb-5 flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold dark:text-white-light">Останні голосування</h2>
                        <p class="text-sm text-white-dark">Активності для учасників.</p>
                    </div>
                    <a href="{{ route('votes.index') }}" class="btn btn-outline-primary btn-sm">Перейти</a>
                </div>

                <div class="space-y-3">
                    @forelse($latestVotes as $vote)
                        <div class="rounded border border-white-light p-3 dark:border-[#1b2e4b]">
                            <div class="font-semibold text-black dark:text-white">{{ $vote->name }}</div>
                            <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-white-dark">
                                <span>{{ $vote->type ?? 'team' }}</span>
                                <span>•</span>
                                <a href="{{ route('votes.result', $vote->vote_url) }}" class="text-primary">Результат</a>
                            </div>
                        </div>
                    @empty
                        <div class="rounded border border-white-light p-4 text-center text-white-dark dark:border-[#1b2e4b]">
                            Голосувань ще немає.
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
@endsection
