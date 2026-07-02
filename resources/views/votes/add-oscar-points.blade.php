@extends('layouts.app')

@include('votes.partials.admin-points-styles')

@section('page-title', 'Бали Оскар')

@section('content')
    <div class="points-page">
        <section class="points-hero">
            <div>
                <p class="points-kicker">Cherry Camp Awards</p>
                <h1 class="points-title">{{ $vote->name }}</h1>
                <p class="points-copy">Додавайте бали журі окремо в потрібній номінації. Картки показують тільки кандидатів, доступних для цієї категорії.</p>
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

        <div class="grid gap-5">
            @foreach($nominations as $key => $nomination)
                @php
                    $candidates = $candidatesByNomination[$key] ?? collect();
                    $isOldNomination = old('nomination') === $key;
                @endphp

                <form class="points-card" action="{{ route('votes.addPoints', $vote->vote_url) }}" method="POST">
                    @csrf
                    <input type="hidden" name="nomination" value="{{ $key }}">

                    <div class="points-card-header">
                        <div>
                            <h2 class="points-card-title">{{ $nomination['title'] }}</h2>
                            <p class="points-card-copy">Оберіть номінанта та вкажіть кількість балів для цієї номінації.</p>
                        </div>
                        <span class="points-badge">{{ $candidates->count() }} кандидатів</span>
                    </div>

                    @if($candidates->isEmpty())
                        <div class="points-empty">У цій номінації немає номінантів.</div>
                    @else
                        <div class="points-choice-grid oscar-admin-grid">
                            @foreach($candidates as $candidate)
                                <label class="points-choice" style="--choice-color: #d4af37;">
                                    <input
                                        class="points-choice-input"
                                        type="radio"
                                        name="nominee_user_id"
                                        value="{{ $candidate->id }}"
                                        @checked($isOldNomination && (string) old('nominee_user_id') === (string) $candidate->id)
                                        required
                                    >
                                    <span class="points-choice-card nominee-choice-card">
                                        <span class="points-check" aria-hidden="true">✓</span>
                                        <img src="{{ $candidate->image_url }}" alt="{{ $candidate->name }}">
                                        <span class="points-choice-title">{{ $candidate->name }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>

                        <div class="points-submit-panel">
                            <div>
                                <label class="points-label" for="points_{{ $key }}">Бали</label>
                                <input
                                    type="number"
                                    id="points_{{ $key }}"
                                    name="points"
                                    class="form-input"
                                    min="1"
                                    value="{{ $isOldNomination ? old('points', 1) : 1 }}"
                                    required
                                >
                            </div>
                            <button type="submit" class="btn btn-primary">Додати бали</button>
                        </div>
                    @endif
                </form>
            @endforeach
        </div>
    </div>
@endsection
