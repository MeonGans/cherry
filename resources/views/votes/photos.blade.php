@extends('layouts.app')

@section('content')
    <div class="panel">
        <div class="mb-5 flex flex-wrap items-start justify-between gap-3">
            <div>
                <h5 class="text-lg font-semibold dark:text-white-light">Фото голосування: {{ $vote->name }}</h5>
                <p class="mt-1 text-sm text-white-dark">Заявки від учнів і відбір фінальних 10 фото.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('votes.show', $vote->vote_url) }}" class="btn btn-outline-info">Голосування</a>
                <a href="{{ route('votes.result', $vote->vote_url) }}" class="btn btn-outline-primary">Результат</a>
                <a href="{{ route('votes.index') }}" class="btn btn-outline-secondary">До списку</a>
            </div>
        </div>

        @if($errors->any())
            <div class="mb-4 rounded border border-danger bg-danger/10 p-3 text-danger">
                {{ $errors->first() }}
            </div>
        @endif

        @if(session('success'))
            <div class="mb-4 rounded border border-success bg-success/10 p-3 text-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-5 grid gap-4 md:grid-cols-3">
            <div class="rounded border border-white-light p-4 dark:border-[#191e3a]">
                <div class="text-xs font-semibold uppercase text-white-dark">Подано фото</div>
                <div class="mt-2 text-2xl font-bold text-black dark:text-white">{{ $photos->count() }}</div>
            </div>
            <div class="rounded border border-white-light p-4 dark:border-[#191e3a]">
                <div class="text-xs font-semibold uppercase text-white-dark">У фіналі</div>
                <div id="finalistCounterCard" class="mt-2 text-2xl font-bold text-primary">{{ $finalistCount }} / 10</div>
            </div>
            <div class="rounded border border-white-light p-4 dark:border-[#191e3a]">
                <div class="text-xs font-semibold uppercase text-white-dark">Веб-версії</div>
                <div class="mt-2 text-sm font-semibold text-black dark:text-white">Оригінал + preview для кожного фото</div>
            </div>
        </div>

        <div class="mb-5 rounded border border-white-light p-4 dark:border-[#191e3a]">
            <label for="upload_url" class="mb-2 block font-semibold">Посилання для завантаження фото учнями</label>
            <div class="flex flex-col gap-2 md:flex-row">
                <input id="upload_url" class="form-input" type="text" value="{{ $uploadUrl }}" readonly>
                <a href="{{ $uploadUrl }}" target="_blank" class="btn btn-outline-primary">Відкрити</a>
                <button id="copyUploadUrl" type="button" class="btn btn-primary">Скопіювати</button>
            </div>
        </div>

        <div class="mb-5 rounded border border-white-light p-4 dark:border-[#191e3a]">
            <form action="{{ route('votes.photos.store', $vote->vote_url) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <label for="photos" class="mb-2 block font-semibold">Додати фото адміном одразу у фінал</label>
                <input
                    type="file"
                    id="photos"
                    name="photos[]"
                    class="form-input"
                    accept="image/png,image/jpeg,image/webp"
                    multiple
                >
                <p class="mt-2 text-xs text-white-dark">Ліміт для фіналу: 10 фото. Оригінал збережеться для друку, preview створиться автоматично.</p>
                <button type="submit" class="btn btn-outline-primary mt-4">Завантажити</button>
            </form>
        </div>

        <form action="{{ route('votes.photos.finalists', $vote->vote_url) }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h6 class="text-base font-semibold text-black dark:text-white">Вибір фіналістів</h6>
                    <p id="finalistCounterText" class="mt-1 text-sm text-white-dark">Обрано {{ $finalistCount }} з 10 фото.</p>
                </div>
                <button type="submit" class="btn btn-primary">Зберегти фіналістів</button>
            </div>

            @if($photos->isEmpty())
                <div class="rounded border border-dashed border-white-light p-6 text-center text-white-dark dark:border-[#191e3a]">
                    Фото ще не завантажено.
                </div>
            @else
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach($photos as $photo)
                        <div class="overflow-hidden rounded border border-white-light bg-white dark:border-[#191e3a] dark:bg-[#0e1726]">
                            <div class="relative bg-[#f3f5f9] dark:bg-[#111827]">
                                <img
                                    src="{{ asset($photo->image_path) }}"
                                    alt="{{ $photo->title ?? 'Фото ' . $loop->iteration }}"
                                    class="h-72 w-full object-contain"
                                >
                                @if($photo->is_finalist)
                                    <span class="absolute left-3 top-3 rounded bg-primary px-2 py-1 text-xs font-bold text-white">Фінал</span>
                                @endif
                            </div>
                            <div class="space-y-3 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="font-semibold text-black dark:text-white">
                                            {{ $photo->user?->name ?? 'Адмінське фото' }}
                                        </div>
                                        <div class="text-xs text-white-dark">
                                            {{ $photo->created_at?->format('d.m.Y H:i') }}
                                        </div>
                                    </div>
                                    <label class="inline-flex cursor-pointer items-center gap-2">
                                        <input
                                            type="checkbox"
                                            name="photo_ids[]"
                                            value="{{ $photo->id }}"
                                            class="form-checkbox finalist-checkbox"
                                            @checked($photo->is_finalist)
                                        >
                                        <span class="font-semibold">Фінал</span>
                                    </label>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('votes.photos.print', $photo) }}" target="_blank" class="btn btn-outline-warning btn-sm">Друк</a>
                                    <a href="{{ asset($photo->print_image_path) }}" target="_blank" class="btn btn-outline-info btn-sm">Оригінал</a>
                                    <a href="{{ asset($photo->image_path) }}" target="_blank" class="btn btn-outline-secondary btn-sm">Preview</a>
                                    <button
                                        type="submit"
                                        form="delete-photo-{{ $photo->id }}"
                                        class="btn btn-outline-danger btn-sm delete-photo-button"
                                    >
                                        Видалити
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </form>

        @foreach($photos as $photo)
            <form id="delete-photo-{{ $photo->id }}" action="{{ route('votes.photos.destroy', $photo) }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        @endforeach
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const checkboxes = Array.from(document.querySelectorAll('.finalist-checkbox'));
            const counterCard = document.getElementById('finalistCounterCard');
            const counterText = document.getElementById('finalistCounterText');
            const copyButton = document.getElementById('copyUploadUrl');
            const uploadInput = document.getElementById('upload_url');
            const limit = 10;

            const syncFinalists = () => {
                const selected = checkboxes.filter((checkbox) => checkbox.checked).length;

                if (counterCard) {
                    counterCard.textContent = `${selected} / ${limit}`;
                }

                if (counterText) {
                    counterText.textContent = `Обрано ${selected} з ${limit} фото.`;
                }

                checkboxes.forEach((checkbox) => {
                    checkbox.disabled = selected >= limit && !checkbox.checked;
                });
            };

            checkboxes.forEach((checkbox) => checkbox.addEventListener('change', syncFinalists));
            syncFinalists();

            document.querySelectorAll('.delete-photo-button').forEach((button) => {
                button.addEventListener('click', (event) => {
                    if (!window.confirm('Видалити фото? Після цього учень зможе завантажити нове фото за своїм PIN-кодом.')) {
                        event.preventDefault();
                    }
                });
            });

            copyButton?.addEventListener('click', async () => {
                try {
                    await navigator.clipboard.writeText(uploadInput.value);
                    copyButton.textContent = 'Скопійовано';
                    window.setTimeout(() => copyButton.textContent = 'Скопіювати', 1400);
                } catch (error) {
                    uploadInput.select();
                }
            });
        });
    </script>
@endsection
