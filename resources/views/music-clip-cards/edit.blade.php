@extends('layouts.app')

@section('content')
    <div class="panel">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h5 class="text-lg font-semibold dark:text-white-light">Редагувати картку кліпу</h5>
                <p class="text-sm text-white-dark">Назву та картинку можна змінити тут, кількість - у таблиці.</p>
            </div>
            <a href="{{ route('music-clip-cards.index') }}" class="btn btn-outline-primary">До списку</a>
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
                src="{{ $card->image_url }}"
                alt="{{ $card->name }}"
                class="h-24 w-24 rounded object-cover"
            >
            <div>
                <div class="text-sm text-white-dark">{{ $card->type_label }}</div>
                <div class="text-lg font-semibold">{{ $card->name }}</div>
            </div>
        </div>

        <form action="{{ route('music-clip-cards.update', $card) }}" method="POST" enctype="multipart/form-data" class="grid gap-5 md:grid-cols-2">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="mb-2 block font-semibold">Назва</label>
                <input type="text" name="name" id="name" class="form-input" value="{{ old('name', $card->name) }}" required>
            </div>

            <div>
                <label for="image" class="mb-2 block font-semibold">Нова картинка</label>
                <input type="file" name="image" id="image" class="form-input" accept="image/*">
            </div>

            <div class="flex gap-2 md:col-span-2">
                <button type="submit" class="btn btn-primary">Зберегти</button>
                <a href="{{ route('music-clip-cards.index') }}" class="btn btn-outline-danger">Скасувати</a>
            </div>
        </form>
    </div>
@endsection
