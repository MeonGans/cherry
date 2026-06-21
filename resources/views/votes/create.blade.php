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

        <form action="{{ route('votes.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label for="name">Назва голосування</label>
                <input type="text" id="name" name="name" class="form-input" value="{{ old('name') }}" required>
            </div>

            <div class="mb-4">
                <label for="type">Тип голосування</label>
                <select id="type" name="type" class="form-select">
                    <option value="{{ \App\Models\Vote::TYPE_TEAM }}" @selected(old('type') === \App\Models\Vote::TYPE_TEAM)>Командне голосування</option>
                    <option value="{{ \App\Models\Vote::TYPE_PHOTO }}" @selected(old('type') === \App\Models\Vote::TYPE_PHOTO)>Краще фото заїзду</option>
                </select>
            </div>

            <div id="photoUploadBlock" class="mb-4 hidden rounded border border-white-light p-4 dark:border-[#191e3a]">
                <label for="photos">Фото для голосування</label>
                <input
                    type="file"
                    id="photos"
                    name="photos[]"
                    class="form-input"
                    accept="image/png,image/jpeg,image/webp"
                    multiple
                >
                <p class="mt-2 text-xs text-white-dark">Для типу “Краще фото заїзду” потрібно завантажити рівно 10 фото.</p>
                <p id="photoCounter" class="mt-2 text-sm font-semibold">Обрано: 0 / 10</p>
            </div>

            <button type="submit" class="btn btn-primary mt-6">Створити</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const typeSelect = document.getElementById('type');
            const photoBlock = document.getElementById('photoUploadBlock');
            const photoInput = document.getElementById('photos');
            const photoCounter = document.getElementById('photoCounter');
            const photoType = @json(\App\Models\Vote::TYPE_PHOTO);

            const syncPhotoBlock = () => {
                photoBlock.classList.toggle('hidden', typeSelect.value !== photoType);
            };

            const syncCounter = () => {
                photoCounter.textContent = `Обрано: ${photoInput.files.length} / 10`;
            };

            typeSelect.addEventListener('change', syncPhotoBlock);
            photoInput.addEventListener('change', syncCounter);
            syncPhotoBlock();
            syncCounter();
        });
    </script>
@endsection
