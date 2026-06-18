@extends('layouts.app')

@section('content')
    <div class="panel">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h5 class="text-lg font-semibold dark:text-white-light">Додати картку кліпу</h5>
                <p class="text-sm text-white-dark">Нова картка одразу буде доступна для стрічок “музикальний кліп”.</p>
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

        <form action="{{ route('music-clip-cards.store') }}" method="POST" enctype="multipart/form-data" class="grid gap-5 md:grid-cols-2">
            @csrf

            <div>
                <label for="type" class="mb-2 block font-semibold">Група</label>
                <select name="type" id="type" class="form-select" required>
                    @foreach($types as $typeValue => $label)
                        <option value="{{ $typeValue }}" {{ old('type', $type) === $typeValue ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="name" class="mb-2 block font-semibold">Назва</label>
                <input type="text" name="name" id="name" class="form-input" value="{{ old('name') }}" required>
            </div>

            <div>
                <label for="image" class="mb-2 block font-semibold">Картинка</label>
                <input type="file" name="image" id="image" class="form-input" accept="image/*" required>
            </div>

            <div>
                <label for="quantity" class="mb-2 block font-semibold">Шанс появи</label>
                <input type="number" name="quantity" id="quantity" class="form-input" min="0" step="1" value="{{ old('quantity', 0) }}" required>
            </div>

            <div class="flex gap-2 md:col-span-2">
                <button type="submit" class="btn btn-primary">Додати картку</button>
                <a href="{{ route('music-clip-cards.index') }}" class="btn btn-outline-danger">Скасувати</a>
            </div>
        </form>
    </div>
@endsection
