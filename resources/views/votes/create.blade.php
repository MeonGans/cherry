@extends('layouts.app')

@section('content')
    <div class="panel">
        <div class="mb-5 flex items-center justify-between">
            <h5 class="text-lg font-semibold dark:text-white-light">Створити голосування</h5>
        </div>

        @if($errors->any())
            <div class="mb-4 rounded border border-danger bg-danger/10 p-3 text-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('votes.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="name">Назва голосування</label>
                <input type="text" id="name" name="name" class="form-input" value="{{ old('name') }}" required>
            </div>

            <div class="mb-4">
                <label for="type">Тип голосування</label>
                <select id="type" name="type" class="form-select">
                    <option value="{{ \App\Models\Vote::TYPE_TEAM }}" @selected(old('type', \App\Models\Vote::TYPE_TEAM) === \App\Models\Vote::TYPE_TEAM)>Командне голосування</option>
                    <option value="{{ \App\Models\Vote::TYPE_PHOTO }}" @selected(old('type') === \App\Models\Vote::TYPE_PHOTO)>Краще фото заїзду</option>
                    <option value="{{ \App\Models\Vote::TYPE_OSCAR }}" @selected(old('type') === \App\Models\Vote::TYPE_OSCAR)>Оскар</option>
                </select>
            </div>

            <div id="photoInfoBlock" class="mb-4 hidden rounded border border-white-light p-4 dark:border-[#191e3a]">
                <p class="font-semibold text-black dark:text-white">Збір фото перед голосуванням</p>
                <p class="mt-2 text-sm text-white-dark">
                    Після створення зʼявиться унікальне посилання для завантаження. Учні завантажують по одному фото за PIN-кодом, а адмін потім обирає до 10 фіналістів.
                </p>
            </div>

            <div id="oscarInfoBlock" class="mb-4 hidden rounded border border-warning p-4 dark:border-warning">
                <p class="font-semibold">Номінації: оператор, монтаж, жіноча роль, чоловіча роль, режисер.</p>
                @if($activeSession)
                    <p class="mt-2 text-sm text-white-dark">
                        Кандидати підтягуються з активної сесії #{{ $activeSession->id }}
                        ({{ $activeSession->start_date }} - {{ $activeSession->end_date }}).
                    </p>

                    <div class="mt-5 space-y-4">
                        @foreach(\App\Models\Vote::OSCAR_NOMINATIONS as $key => $nomination)
                            @php
                                $candidates = $oscarCandidatesByNomination[$key] ?? collect();
                                $selected = old("oscar_nominees.{$key}", []);
                            @endphp

                            <div class="rounded border border-white-light p-3 dark:border-[#191e3a]">
                                <label for="oscar_nominees_{{ $key }}" class="mb-2 block font-semibold">
                                    {{ $nomination['title'] }}
                                    <span class="text-xs font-normal text-white-dark">
                                        мінімум {{ $nomination['limit'] }}
                                    </span>
                                </label>

                                <select
                                    id="oscar_nominees_{{ $key }}"
                                    name="oscar_nominees[{{ $key }}][]"
                                    class="form-select oscar-multiselect"
                                    multiple
                                    size="6"
                                >
                                    @foreach($candidates as $candidate)
                                        <option value="{{ $candidate->id }}" @selected(in_array($candidate->id, $selected))>
                                            {{ $candidate->name }}
                                        </option>
                                    @endforeach
                                </select>

                                @if($candidates->isEmpty())
                                    <p class="mt-2 text-sm font-semibold text-danger">Немає кандидатів для цієї номінації.</p>
                                @else
                                    <p class="mt-2 text-xs text-white-dark">
                                        Обрано: <span class="oscar-selected-count">0</span> / {{ $candidates->count() }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="mt-2 text-sm font-semibold text-danger">
                        Активної сесії немає. Щоб створити “Оскар”, спочатку активуйте потрібний заїзд.
                    </p>
                @endif
            </div>

            <button type="submit" class="btn btn-primary mt-6">Створити</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const typeSelect = document.getElementById('type');
            const photoBlock = document.getElementById('photoInfoBlock');
            const oscarBlock = document.getElementById('oscarInfoBlock');
            const oscarMultiselects = Array.from(document.querySelectorAll('.oscar-multiselect'));
            const photoType = @json(\App\Models\Vote::TYPE_PHOTO);
            const oscarType = @json(\App\Models\Vote::TYPE_OSCAR);

            const syncBlocks = () => {
                photoBlock.classList.toggle('hidden', typeSelect.value !== photoType);
                oscarBlock.classList.toggle('hidden', typeSelect.value !== oscarType);
            };

            const syncOscarCounters = () => {
                oscarMultiselects.forEach((select) => {
                    const counter = select.closest('div').querySelector('.oscar-selected-count');

                    if (counter) {
                        counter.textContent = Array.from(select.selectedOptions).length;
                    }
                });
            };

            typeSelect.addEventListener('change', syncBlocks);
            oscarMultiselects.forEach((select) => select.addEventListener('change', syncOscarCounters));
            syncBlocks();
            syncOscarCounters();
        });
    </script>
@endsection
