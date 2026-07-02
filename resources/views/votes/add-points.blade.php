@extends('layouts.app')

@include('votes.partials.admin-points-styles')

@section('page-title', 'Додати бали')

@section('content')
    <div class="points-page">
        <section class="points-hero">
            <div>
                <p class="points-kicker">Командне голосування</p>
                <h1 class="points-title">{{ $vote->name }}</h1>
                <p class="points-copy">Оберіть команду за логотипом стихії та додайте потрібну кількість балів вручну.</p>
            </div>
            <div class="points-actions">
                <a href="{{ route('votes.index') }}" class="btn btn-outline-primary">До списку</a>
                <a href="{{ route('votes.result', $vote->vote_url) }}" class="btn btn-primary">Результат</a>
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
                    <h2 class="points-card-title">Команда</h2>
                    <p class="points-card-copy">Клік по картці одразу обирає команду для нарахування.</p>
                </div>
                <span class="points-badge">{{ $teams->count() }} команд</span>
            </div>

            @if($teams->isEmpty())
                <div class="points-empty">Немає команд для додавання балів.</div>
            @else
                <div class="points-choice-grid">
                    @foreach($teams as $team)
                        <label class="points-choice" style="--choice-color: {{ $team->element?->color ?? '#d9234e' }};">
                            <input
                                class="points-choice-input"
                                type="radio"
                                name="team_id"
                                value="{{ $team->id }}"
                                @checked((string) old('team_id') === (string) $team->id)
                                required
                            >
                            <span class="points-choice-card">
                                <span class="points-check" aria-hidden="true">✓</span>
                                <span class="team-choice-logo">
                                    <img src="{{ $team->element_logo_url }}" alt="{{ $team->element?->name ?? $team->name }}">
                                </span>
                                <span class="points-choice-title">{{ $team->name }}</span>
                                <span class="points-choice-meta">{{ $team->element?->name ?? 'Команда' }}</span>
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
