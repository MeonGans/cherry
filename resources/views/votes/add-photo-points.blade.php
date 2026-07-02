@extends('layouts.app')

@include('votes.partials.admin-points-styles')

@section('page-title', 'Бали журі')

@section('content')
    <div class="points-page">
        <section class="points-hero">
            <div>
                <p class="points-kicker">Фото-голосування</p>
                <h1 class="points-title">{{ $vote->name }}</h1>
                <p class="points-copy">Оберіть фото з галереї та додайте бали журі. Після збереження відкриються результати цього голосування.</p>
            </div>
            <div class="points-actions">
                <a href="{{ route('votes.photosForm', $vote->vote_url) }}" class="btn btn-outline-info">Фото</a>
                <a href="{{ route('votes.result', $vote->vote_url) }}" class="btn btn-primary">Результат</a>
                <a href="{{ route('votes.result', ['voteUrl' => $vote->vote_url, 'scores' => 1]) }}" class="btn btn-outline-warning">З балами</a>
            </div>
        </section>

        @if($errors->any())
            <div class="points-error" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <form class="points-card" action="{{ route('votes.addPoints', $vote->vote_url) }}" method="POST">
            @csrf

            <div class="points-card-header">
                <div>
                    <h2 class="points-card-title">Фото</h2>
                    <p class="points-card-copy">Виберіть одну фотографію, якій потрібно додати бали.</p>
                </div>
                <span class="points-badge">{{ $photos->count() }} фото</span>
            </div>

            @if($photos->isEmpty())
                <div class="points-empty">Для цього голосування ще немає фото.</div>
            @else
                <div class="points-choice-grid">
                    @foreach($photos as $photo)
                        <label class="points-choice" style="--choice-color: #4361ee;">
                            <input
                                class="points-choice-input"
                                type="radio"
                                name="vote_photo_id"
                                value="{{ $photo->id }}"
                                @checked((string) old('vote_photo_id') === (string) $photo->id)
                                required
                            >
                            <span class="points-choice-card photo-choice-card">
                                <span class="points-check" aria-hidden="true">✓</span>
                                <img src="{{ asset($photo->image_path) }}" alt="{{ $photo->title ?? 'Фото ' . $loop->iteration }}">
                                <span class="points-choice-title">{{ $photo->title ?? 'Фото ' . $loop->iteration }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>

                <div class="points-submit-panel">
                    <div>
                        <label class="points-label" for="points">Бали</label>
                        <input type="number" id="points" name="points" class="form-input" min="1" value="{{ old('points', 1) }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Додати бали</button>
                </div>
            @endif
        </form>
    </div>
@endsection
