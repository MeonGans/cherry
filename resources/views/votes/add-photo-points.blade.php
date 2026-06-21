@extends('layouts.app')

@section('content')
    <div class="panel">
        <div class="mb-5 flex items-center justify-between">
            <h5 class="text-lg font-semibold dark:text-white-light">Додати бали журі: {{ $vote->name }}</h5>
            <a href="{{ route('votes.result', $vote->vote_url) }}" class="btn btn-outline-primary">Результат</a>
        </div>

        @if($errors->any())
            <div class="mb-4 rounded border border-danger bg-danger/10 p-3 text-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('votes.addPoints', $vote->vote_url) }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="vote_photo_id">Фото</label>
                <select class="selectize" name="vote_photo_id" id="vote_photo_id" required>
                    @foreach($photos as $photo)
                        <option value="{{ $photo->id }}">{{ $photo->title ?? 'Фото ' . $loop->iteration }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="points">Бали</label>
                <input type="number" id="points" name="points" class="form-input" min="1" required>
            </div>

            <button type="submit" class="btn btn-primary">Додати бали</button>
        </form>

        <div class="mt-6 grid grid-cols-2 gap-4 md:grid-cols-5">
            @foreach($photos as $photo)
                <div class="overflow-hidden rounded border border-white-light dark:border-[#191e3a]">
                    <img src="{{ asset($photo->image_path) }}" alt="{{ $photo->title ?? 'Фото ' . $loop->iteration }}" class="h-32 w-full object-cover">
                    <div class="p-2 text-center font-semibold">{{ $photo->title ?? 'Фото ' . $loop->iteration }}</div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
