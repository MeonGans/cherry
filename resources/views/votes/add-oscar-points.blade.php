@extends('layouts.app')

@include('votes.partials.admin-points-styles')

@section('page-title', 'Бали Оскар')

@section('content')
    <div class="points-page">
        <section class="points-hero">
            <div>
                <p class="points-kicker">Cherry Camp Awards</p>
                <h1 class="points-title">{{ $vote->name }}</h1>
                <p class="points-copy">Заповніть бали в будь-яких номінаціях і збережіть усе одним натисканням. Порожні поля та нулі не додаються.</p>
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

        <form class="grid gap-5" action="{{ route('votes.addPoints', $vote->vote_url) }}" method="POST">
            @csrf

            @foreach($nominations as $key => $nomination)
                @php
                    $candidates = $candidatesByNomination[$key] ?? collect();
                @endphp

                <section class="points-card">
                    <div class="points-card-header">
                        <div>
                            <h2 class="points-card-title">{{ $nomination['title'] }}</h2>
                            <p class="points-card-copy">Вкажіть бали біля одного або кількох номінантів.</p>
                        </div>
                        <span class="points-badge">{{ $candidates->count() }} кандидатів</span>
                    </div>

                    @if($candidates->isEmpty())
                        <div class="points-empty">У цій номінації немає номінантів.</div>
                    @else
                        <div class="points-choice-grid oscar-admin-grid">
                            @foreach($candidates as $candidate)
                                <div class="points-choice" style="--choice-color: #d4af37;">
                                    <div class="points-choice-card nominee-choice-card points-score-card">
                                        <img src="{{ $candidate->image_url }}" alt="{{ $candidate->name }}">
                                        <span class="points-choice-title">{{ $candidate->name }}</span>
                                        <span class="points-current-score">
                                            Зараз: {{ (int) ($scoreTotals[$key . ':' . $candidate->id] ?? 0) }}
                                        </span>
                                        <div class="points-score-footer">
                                            <label class="points-score-label" for="points_{{ $key }}_{{ $candidate->id }}">Бали</label>
                                            <input
                                                type="number"
                                                id="points_{{ $key }}_{{ $candidate->id }}"
                                                name="points[{{ $key }}][{{ $candidate->id }}]"
                                                class="form-input points-score-input"
                                                min="0"
                                                step="1"
                                                value="{{ old("points.{$key}.{$candidate->id}") }}"
                                                placeholder="0"
                                            >
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>
            @endforeach

            <div class="points-submit-panel points-submit-panel-sticky">
                <div>
                    <div class="points-label">Пакетне додавання</div>
                    <p class="points-submit-copy">Збережуться всі заповнені номінанти в усіх номінаціях.</p>
                </div>
                <button type="submit" class="btn btn-primary">Додати всі бали</button>
            </div>
        </form>
    </div>
@endsection
