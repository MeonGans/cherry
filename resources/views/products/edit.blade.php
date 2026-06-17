@extends('layouts.app')

@section('content')
    <div class="panel">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h5 class="text-lg font-semibold dark:text-white-light">Редагувати товар</h5>
                <p class="text-sm text-white-dark">Назву та фото можна змінити тут, кількість і цінність - у таблиці.</p>
            </div>
            <a href="{{ route('products.index') }}" class="btn btn-outline-primary">До списку</a>
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
                src="{{ $product->image_url }}"
                alt="{{ $product->name }}"
                class="h-24 w-24 rounded object-cover"
            >
            <div>
                <div class="text-sm text-white-dark">Поточний товар</div>
                <div class="text-lg font-semibold">{{ $product->name }}</div>
            </div>
        </div>

        <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data" class="grid gap-5 md:grid-cols-2">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="mb-2 block font-semibold">Назва</label>
                <input type="text" name="name" id="name" class="form-input" value="{{ old('name', $product->name) }}" required>
            </div>

            <div>
                <label for="image" class="mb-2 block font-semibold">Нове фото</label>
                <input type="file" name="image" id="image" class="form-input" accept="image/*">
            </div>

            <div class="flex gap-2 md:col-span-2">
                <button type="submit" class="btn btn-primary">Зберегти</button>
                <a href="{{ route('products.index') }}" class="btn btn-outline-danger">Скасувати</a>
            </div>
        </form>
    </div>
@endsection
