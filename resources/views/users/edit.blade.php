@extends('layouts.app')

@section('content')
    <div class="panel">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h5 class="text-lg font-semibold dark:text-white-light">Редагувати учня</h5>
                <p class="text-sm text-white-dark">Оновіть ім'я, PIN-код або фото учня заїзду.</p>
            </div>
            <a href="{{ route('list') }}" class="btn btn-outline-primary">До списку</a>
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

        <div class="mb-5 flex items-center gap-4">
            <img
                src="{{ $user->image_url }}"
                alt="{{ $user->name }}"
                class="h-24 w-24 rounded object-cover"
            >
            <div>
                <div class="text-sm text-white-dark">Поточний учень</div>
                <div class="text-lg font-semibold">{{ $user->name }}</div>
                <div class="text-sm text-white-dark">{{ $user->team->name ?? 'Без команди' }}</div>
            </div>
        </div>

        <form action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data" class="grid gap-5 md:grid-cols-2">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="mb-2 block font-semibold">Ім'я</label>
                <input type="text" name="name" id="name" class="form-input" value="{{ old('name', $user->name) }}" required>
            </div>

            <div>
                <label for="pin_code" class="mb-2 block font-semibold">PIN-код</label>
                <input type="text" name="pin_code" id="pin_code" class="form-input" value="{{ old('pin_code', $user->pin_code) }}">
            </div>

            <div>
                <label for="desired_team_id" class="mb-2 block font-semibold">Очікувана команда</label>
                <select name="desired_team_id" id="desired_team_id" class="form-select">
                    <option value="">Без команди</option>
                    @foreach($teams as $team)
                        <option value="{{ $team->id }}" {{ (string) old('desired_team_id', $user->desired_team_id) === (string) $team->id ? 'selected' : '' }}>
                            #{{ $team->id }} - {{ $team->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="gender" class="mb-2 block font-semibold">Стать</label>
                <select name="gender" id="gender" class="form-select" required>
                    <option value="female" @selected(old('gender', $user->gender) === 'female')>Дівчина</option>
                    <option value="male" @selected(old('gender', $user->gender) === 'male')>Хлопець</option>
                </select>
            </div>

            <div>
                <label for="image" class="mb-2 block font-semibold">Нове фото</label>
                <input type="file" name="image" id="image" class="form-input" accept="image/*">
                <p class="mt-2 text-xs text-white-dark">Підтримуються JPG, PNG або WEBP до 15 МБ. Фото буде оптимізовано під час збереження.</p>
            </div>

            <div class="flex gap-2 md:col-span-2">
                <button type="submit" class="btn btn-primary">Зберегти</button>
                <a href="{{ route('list') }}" class="btn btn-outline-danger">Скасувати</a>
            </div>
        </form>
    </div>
@endsection
