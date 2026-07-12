@extends('layouts.vote')

@section('title', 'Завантаження фото - CHERRY CAMP')

@section('styles')
    <style>
        .upload-hint-list {
            display: grid;
            gap: 8px;
            margin: 20px 0 0;
            padding: 0;
            color: var(--vote-muted);
            font-weight: 800;
            list-style: none;
        }

        .upload-file {
            display: block;
            width: 100%;
            min-height: 56px;
            border: 1px dashed var(--vote-line);
            border-radius: 14px;
            background: #ffffff;
            color: var(--vote-ink);
            font: inherit;
            font-weight: 800;
            padding: 15px;
        }

        .upload-grid {
            display: grid;
            gap: 18px;
        }
    </style>
@endsection

@section('content')
    <section class="vote-card">
        <div class="vote-card-inner">
            <span class="vote-kicker">Фото заїзду</span>
            <h1 class="vote-title">{{ $vote->name }}</h1>
            <p class="vote-copy">
                Введіть свій PIN-код і завантажте одну фотографію в хорошій якості. Для веб-перегляду система зробить легшу копію, а оригінал залишиться для друку.
            </p>

            @if($errors->any())
                <div class="vote-error" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            @if(session('success'))
                <div class="vote-success" role="status">
                    {{ session('success') }}
                </div>
            @endif

            <form class="vote-form upload-grid" action="{{ route('votes.photoUpload.store', $vote->vote_url) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="vote-field">
                    <label class="vote-label" for="pin_code">PIN-код</label>
                    <input
                        class="vote-input"
                        id="pin_code"
                        name="pin_code"
                        type="text"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        value="{{ old('pin_code') }}"
                        required
                        autofocus
                    >
                </div>

                <div class="vote-field">
                    <label class="vote-label" for="photo">Фото</label>
                    <input
                        class="upload-file"
                        id="photo"
                        name="photo"
                        type="file"
                        accept="image/png,image/jpeg,image/webp"
                        required
                    >
                </div>

                <ul class="upload-hint-list">
                    <li>Формати: JPG, PNG або WebP.</li>
                    <li>Можна завантажити лише одне фото за PIN-кодом.</li>
                    <li>Максимальний розмір файлу: 30 МБ.</li>
                </ul>

                <div class="vote-actions">
                    <button type="submit" class="vote-button">Завантажити фото</button>
                </div>
            </form>
        </div>
    </section>
@endsection
