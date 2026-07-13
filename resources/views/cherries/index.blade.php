@extends('layouts.app')

@section('page-title', 'Черіки')

@section('content')
    <style>
        .cherry-summary-card { position: relative; overflow: hidden; }
        .cherry-summary-card::after { content: ''; position: absolute; right: -30px; bottom: -36px; width: 110px; height: 110px; border-radius: 999px; background: rgba(225, 29, 72, .08); }
        .cherry-count { font-variant-numeric: tabular-nums; }
        .cherry-adjust { display: grid; grid-template-columns: 44px minmax(72px, 96px) 44px; gap: 6px; }
        .cherry-adjust button { display: grid; min-height: 42px; place-items: center; border-radius: 8px; color: white; font-size: 1.25rem; font-weight: 900; }
        .cherry-adjust input, .cherry-exact input { min-height: 42px; border: 1px solid #e0e6ed; border-radius: 8px; background: transparent; padding: 0 10px; text-align: center; font-weight: 800; }
        .dark .cherry-adjust input, .dark .cherry-exact input { border-color: #253b5c; }
        .cherry-exact { display: flex; gap: 6px; }
        .cherry-exact input { width: 110px; }
        .cherry-exact button { min-height: 42px; border-radius: 8px; padding: 0 12px; }
        @media (max-width: 760px) {
            .cherry-table thead { display: none; }
            .cherry-table, .cherry-table tbody, .cherry-table tr, .cherry-table td { display: block; width: 100%; }
            .cherry-table tr { border-bottom: 1px solid #e0e6ed; padding: 14px 0; }
            .dark .cherry-table tr { border-color: #253b5c; }
            .cherry-table td { border: 0 !important; padding: 6px 0 !important; }
            .cherry-controls { align-items: flex-start !important; flex-direction: column; }
        }
    </style>

    <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-danger">Валюта заїзду</p>
            <h1 class="mt-1 text-3xl font-black text-black dark:text-white">Облік Черіків</h1>
            @if($session)
                <p class="mt-1 text-sm text-white-dark">
                    Заїзд {{ \Illuminate\Support\Carbon::parse($session->start_date)->format('d.m.Y') }}–{{ \Illuminate\Support\Carbon::parse($session->end_date)->format('d.m.Y') }}
                </p>
            @endif
        </div>

        @if($session)
            <a href="{{ route('cherries.result') }}" target="_blank" rel="noopener" class="btn btn-danger">
                Відкрити оголошення результатів ↗
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-5 rounded border border-success bg-success-light p-3 text-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="mb-5 rounded border border-danger bg-danger-light p-3 text-danger">{{ $errors->first() }}</div>
    @endif

    @if(!$session)
        <section class="panel py-12 text-center">
            <div class="text-xl font-bold text-black dark:text-white">Немає активного заїзду</div>
            <p class="mt-2 text-white-dark">Активуйте заїзд, щоб почати облік Черіків.</p>
            <a href="{{ route('sessions.index') }}" class="btn btn-primary mt-5">До заїздів</a>
        </section>
    @else
        <div class="mb-6 grid gap-4 md:grid-cols-3">
            <article class="cherry-summary-card panel">
                <p class="text-xs font-bold uppercase text-white-dark">Зібрано всього</p>
                <div class="cherry-count mt-2 text-4xl font-black text-danger">{{ number_format($grandTotal, 0, ',', ' ') }}</div>
                <p class="mt-1 text-sm text-white-dark">Черіків</p>
            </article>
            <article class="cherry-summary-card panel">
                <p class="text-xs font-bold uppercase text-white-dark">Учасників</p>
                <div class="mt-2 text-4xl font-black text-black dark:text-white">{{ $users->count() }}</div>
                <p class="mt-1 text-sm text-white-dark">у поточному заїзді</p>
            </article>
            <article class="cherry-summary-card panel">
                <p class="text-xs font-bold uppercase text-white-dark">Команд</p>
                <div class="mt-2 text-4xl font-black text-black dark:text-white">{{ $teamTotals->count() }}</div>
                <p class="mt-1 text-sm text-white-dark">беруть участь</p>
            </article>
        </div>

        <section class="panel mb-6">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-bold text-black dark:text-white">Підсумки команд</h2>
                    <p class="text-sm text-white-dark">Оновлюються автоматично із сум учасників.</p>
                </div>
            </div>
            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                @forelse($teamTotals as $teamTotal)
                    <article class="flex items-center gap-3 rounded border border-white-light p-4 dark:border-[#1b2e4b]">
                        <img src="{{ $teamTotal['team']->element_logo_url }}" alt="" class="h-14 w-14 rounded-lg object-contain">
                        <div class="min-w-0">
                            <div class="truncate font-bold text-black dark:text-white">{{ $teamTotal['team']->name }}</div>
                            <div class="cherry-count mt-1 text-2xl font-black text-danger">{{ number_format($teamTotal['total'], 0, ',', ' ') }}</div>
                            <div class="text-xs text-white-dark">{{ $teamTotal['members_count'] }} учасників</div>
                        </div>
                    </article>
                @empty
                    <p class="text-white-dark">Учасники ще не розподілені по командах.</p>
                @endforelse
            </div>
        </section>

        <section class="panel">
            <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h2 class="text-lg font-bold text-black dark:text-white">Черіки учасників</h2>
                    <p class="text-sm text-white-dark">Оберіть суму і натисніть − або +. Точне поле повністю замінює поточне значення.</p>
                </div>
                <label class="w-full sm:w-72">
                    <span class="sr-only">Пошук учасника</span>
                    <input id="cherrySearch" type="search" class="form-input" placeholder="Пошук за ім’ям або командою">
                </label>
            </div>

            <div class="table-responsive">
                <table class="cherry-table table-hover w-full">
                    <thead>
                        <tr>
                            <th>Учасник</th>
                            <th>Команда</th>
                            <th class="text-center">Зараз</th>
                            <th>Додати / відняти</th>
                            <th>Точне значення</th>
                        </tr>
                    </thead>
                    <tbody id="cherryRows">
                        @forelse($users as $user)
                            <tr data-search="{{ \Illuminate\Support\Str::lower($user->name . ' ' . ($user->team?->name ?? '')) }}">
                                <td>
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $user->image_url }}" alt="" class="h-11 w-11 rounded-full object-cover">
                                        <span class="font-bold text-black dark:text-white">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $user->team?->name ?? 'Без команди' }}</td>
                                <td class="cherry-count text-center text-2xl font-black text-danger">{{ number_format($user->cherries, 0, ',', ' ') }}</td>
                                <td>
                                    <form action="{{ route('cherries.adjust', $user) }}" method="POST" class="cherry-adjust">
                                        @csrf
                                        <button name="direction" value="subtract" type="submit" class="bg-danger" aria-label="Відняти Черіки">−</button>
                                        <input name="amount" type="number" min="1" max="1000000000" value="10" required aria-label="Кількість Черіків">
                                        <button name="direction" value="add" type="submit" class="bg-success" aria-label="Додати Черіки">+</button>
                                    </form>
                                </td>
                                <td>
                                    <form action="{{ route('cherries.update', $user) }}" method="POST" class="cherry-exact">
                                        @csrf
                                        @method('PUT')
                                        <input name="amount" type="number" min="0" max="1000000000" value="{{ $user->cherries }}" required aria-label="Точна кількість Черіків">
                                        <button type="submit" class="btn btn-outline-primary">Зберегти</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-8 text-center text-white-dark">У цьому заїзді ще немає учасників.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const search = document.getElementById('cherrySearch');
            if (!search) return;

            search.addEventListener('input', () => {
                const term = search.value.trim().toLocaleLowerCase('uk');
                document.querySelectorAll('#cherryRows tr[data-search]').forEach((row) => {
                    row.hidden = !row.dataset.search.includes(term);
                });
            });
        });
    </script>
@endsection
