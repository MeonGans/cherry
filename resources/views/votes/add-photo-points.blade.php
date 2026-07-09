@extends('layouts.app')

@include('votes.partials.admin-points-styles')

@section('page-title', 'Бали журі')

@section('content')
    <div class="points-page">
        <section class="points-hero">
            <div>
                <p class="points-kicker">Фото-голосування</p>
                <h1 class="points-title">{{ $vote->name }}</h1>
                <p class="points-copy">Поставте бали одразу кільком фото та збережіть їх одним натисканням. Порожні поля та нулі не додаються.</p>
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

        @if(session('success'))
            <div class="points-success" role="status">
                {{ session('success') }}
            </div>
        @endif

        <form class="points-card" action="{{ route('votes.addPoints', $vote->vote_url) }}" method="POST">
            @csrf

            <div class="points-card-header">
                <div>
                    <h2 class="points-card-title">Бали фото</h2>
                    <p class="points-card-copy">Можна оцінити кілька фотографій одразу.</p>
                </div>
                <span class="points-badge">{{ $photos->count() }} фото</span>
            </div>

            @if($photos->isEmpty())
                <div class="points-empty">Для цього голосування ще немає фото.</div>
            @else
                <div class="points-choice-grid">
                    @foreach($photos as $photo)
                        <div class="points-choice" style="--choice-color: #4361ee;">
                            <div class="points-choice-card photo-choice-card points-score-card">
                                <img src="{{ asset($photo->image_path) }}" alt="{{ $photo->title ?? 'Фото ' . $loop->iteration }}">
                                <span class="points-choice-title">{{ $photo->title ?? 'Фото ' . $loop->iteration }}</span>
                                <span class="points-current-score">Зараз: {{ (int) ($scoreTotals[$photo->id] ?? 0) }}</span>
                                <div class="points-score-footer">
                                    <label class="points-score-label" for="points_photo_{{ $photo->id }}">Бали</label>
                                    <input
                                        type="number"
                                        id="points_photo_{{ $photo->id }}"
                                        name="points[{{ $photo->id }}]"
                                        class="form-input points-score-input"
                                        min="0"
                                        step="1"
                                        value="{{ old("points.{$photo->id}") }}"
                                        placeholder="0"
                                    >
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="points-submit-panel">
                    <div>
                        <div class="points-label">Пакетне додавання</div>
                        <p class="points-submit-copy">Заповніть усі потрібні фото перед збереженням.</p>
                    </div>
                    <button type="submit" class="btn btn-primary">Додати всі бали</button>
                </div>
            @endif
        </form>
    </div>
@endsection
