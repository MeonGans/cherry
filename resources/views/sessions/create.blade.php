@extends('layouts.app')

@section('content')
    <div class="panel">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h5 class="text-lg font-semibold dark:text-white-light">Створити сесію</h5>
                <p class="text-sm text-white-dark">Нова сесія матиме окремий список учнів.</p>
            </div>
            <a href="{{ route('sessions.index') }}" class="btn btn-outline-primary">До сесій</a>
        </div>

        @if($errors->any())
            <div class="mb-5 rounded border border-danger bg-danger-light p-3 text-danger">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('sessions.store') }}" method="POST" class="grid gap-5 md:grid-cols-2">
            @csrf

            <div>
                <label for="start_date" class="mb-2 block font-semibold">Дата початку</label>
                <input type="date" name="start_date" id="start_date" class="form-input" value="{{ old('start_date') }}" required>
            </div>

            <div>
                <label for="end_date" class="mb-2 block font-semibold">Дата завершення</label>
                <input type="date" name="end_date" id="end_date" class="form-input" value="{{ old('end_date') }}" required>
            </div>

            <div class="md:col-span-2">
                <label class="inline-flex cursor-pointer items-center gap-2">
                    <input type="checkbox" name="active" value="1" class="form-checkbox" {{ old('active') ? 'checked' : '' }}>
                    <span>Зробити цю сесію активною</span>
                </label>
                <p class="mt-2 text-xs text-white-dark">
                    Якщо увімкнути, інші сесії автоматично стануть неактивними.
                </p>
            </div>

            <div class="md:col-span-2">
                <label for="students" class="mb-2 block font-semibold">Загальний список учнів</label>
                <textarea
                    name="students"
                    id="students"
                    rows="10"
                    class="form-textarea"
                    placeholder="Іванов Іван - 5&#10;Петренко Марія - 2"
                >{{ old('students') }}</textarea>
                <p class="mt-2 text-xs text-white-dark">
                    Формат: ім'я учня, дефіс, ID очікуваної команди. Для кожного учня автоматично створиться PIN-код.
                </p>
            </div>

            <div class="md:col-span-2">
                <div class="rounded border border-white-light p-4 dark:border-[#1b2e4b]">
                    <div class="mb-3 text-sm font-semibold text-black dark:text-white">ID команд</div>
                    <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($teams as $team)
                            <div class="rounded bg-white-light/40 px-3 py-2 text-sm dark:bg-dark">
                                <span class="font-semibold">#{{ $team->id }}</span>
                                <span class="text-white-dark">-</span>
                                <span>{{ $team->name }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex gap-2 md:col-span-2">
                <button type="submit" class="btn btn-primary">Створити</button>
                <a href="{{ route('sessions.index') }}" class="btn btn-outline-danger">Скасувати</a>
            </div>
        </form>
    </div>
@endsection
