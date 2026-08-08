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
                    Після створення з’явиться унікальне посилання для завантаження. Учасники завантажують по одному фото за PIN-кодом, а адміністратор обирає до 10 фіналістів.
                </p>
            </div>

            <div id="oscarInfoBlock" class="mb-4 hidden rounded border border-warning p-4 dark:border-warning">
                @if($activeSession)
                    <p class="font-semibold">Оберіть номінантів активного заїзду #{{ $activeSession->id }} за фото.</p>
                    <p class="mt-1 text-sm text-white-dark">{{ $activeSession->start_date }} — {{ $activeSession->end_date }}</p>
                    @include('votes.partials.oscar-nominee-picker')
                @else
                    <p class="text-sm font-semibold text-danger">
                        Активного заїзду немає. Щоб створити «Оскар», спочатку активуйте потрібний заїзд.
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
            const photoType = @json(\App\Models\Vote::TYPE_PHOTO);
            const oscarType = @json(\App\Models\Vote::TYPE_OSCAR);

            const syncBlocks = () => {
                photoBlock.classList.toggle('hidden', typeSelect.value !== photoType);
                oscarBlock.classList.toggle('hidden', typeSelect.value !== oscarType);
            };

            typeSelect.addEventListener('change', syncBlocks);
            syncBlocks();
        });
    </script>
@endsection
