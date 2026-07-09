@extends('layouts.app')

@include('votes.partials.admin-points-styles')

@section('page-title', 'Додати бали')

@section('content')
    <div class="points-page">
        <section class="points-hero">
            <div>
                <p class="points-kicker">Командне голосування</p>
                <h1 class="points-title">{{ $vote->name }}</h1>
                <p class="points-copy">Вкажіть бали біля потрібних команд і збережіть одним натисканням. Порожні поля та нулі не додаються.</p>
            </div>
            <div class="points-actions">
                <a href="{{ route('votes.index') }}" class="btn btn-outline-primary">До списку</a>
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
                    <h2 class="points-card-title">Бали команд</h2>
                    <p class="points-card-copy">Можна заповнити кілька команд одразу.</p>
                </div>
                <span class="points-badge">{{ $teams->count() }} команд</span>
            </div>

            @if($teams->isEmpty())
                <div class="points-empty">Немає команд для додавання балів.</div>
            @else
                <div class="points-choice-grid">
                    @foreach($teams as $team)
                        <div class="points-choice" style="--choice-color: {{ $team->element?->color ?? '#d9234e' }};">
                            <div class="points-choice-card points-score-card">
                                <span class="team-choice-logo">
                                    <img src="{{ $team->element_logo_url }}" alt="{{ $team->element?->name ?? $team->name }}">
                                </span>
                                <span class="points-choice-title">{{ $team->name }}</span>
                                <span class="points-choice-meta">{{ $team->element?->name ?? 'Команда' }}</span>
                                <span class="points-current-score">Зараз: {{ (int) ($scoreTotals[$team->id] ?? 0) }}</span>
                                <label class="points-score-label" for="points_team_{{ $team->id }}">Бали</label>
                                <input
                                    type="number"
                                    id="points_team_{{ $team->id }}"
                                    name="points[{{ $team->id }}]"
                                    class="form-input points-score-input"
                                    min="0"
                                    step="1"
                                    value="{{ old("points.{$team->id}") }}"
                                    placeholder="0"
                                >
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="points-submit-panel">
                    <div>
                        <div class="points-label">Пакетне додавання</div>
                        <p class="points-submit-copy">Заповніть усі потрібні поля перед збереженням.</p>
                    </div>
                    <button type="submit" class="btn btn-primary">Додати всі бали</button>
                </div>
            @endif
        </form>
    </div>
@endsection
