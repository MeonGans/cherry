@extends('layouts.app')

@section('content')
    <div class="panel">
        <div class="mb-5 flex items-center justify-between">
            <h5 class="text-lg font-semibold dark:text-white-light">Фото голосування: {{ $vote->name }}</h5>
            <a href="{{ route('votes.index') }}" class="btn btn-outline-primary">До списку</a>
        </div>

        @if($errors->any())
            <div class="mb-4 rounded border border-danger bg-danger/10 p-3 text-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="mb-5 rounded border border-white-light p-4 dark:border-[#191e3a]">
            <p class="mb-3 font-semibold">Завантажено: {{ $vote->photos->count() }} / 10</p>

            @if($vote->photos->count() < 10)
                <form action="{{ route('votes.photos.store', $vote->vote_url) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label for="photos">Додати фото</label>
                    <input
                        type="file"
                        id="photos"
                        name="photos[]"
                        class="form-input"
                        accept="image/png,image/jpeg,image/webp"
                        multiple
                        required
                    >
                    <button type="submit" class="btn btn-primary mt-4">Завантажити</button>
                </form>
            @else
                <p class="text-success">Усі 10 фото завантажено.</p>
            @endif
        </div>

        <div class="grid grid-cols-2 gap-4 md:grid-cols-5">
            @forelse($vote->photos as $photo)
                <div class="overflow-hidden rounded border border-white-light dark:border-[#191e3a]">
                    <img src="{{ asset($photo->image_path) }}" alt="{{ $photo->title ?? 'Фото ' . $loop->iteration }}" class="h-40 w-full object-cover">
                    <div class="p-3 text-center font-semibold">{{ $photo->title ?? 'Фото ' . $loop->iteration }}</div>
                </div>
            @empty
                <p>Фото ще не завантажено.</p>
            @endforelse
        </div>
    </div>
@endsection
