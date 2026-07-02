@extends('layouts.vote')

@section('title', $vote->name . ' - CHERRY CAMP')

@section('content')
    <section class="vote-card">
        <div class="vote-card-inner">
            <span class="vote-kicker">Авторизація</span>
            <h1 class="vote-title">{{ $vote->name }}</h1>
            <p class="vote-copy">Введіть свій PIN-код, щоб перейти до голосування. Код потрібен лише для того, щоб зарахувати один голос від кожного учасника.</p>

            @if($errors->any())
                <div class="vote-error" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <form class="vote-form" action="{{ route('votes.authenticate', $vote->vote_url) }}" method="POST">
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
                    />
                </div>

                <div class="vote-actions">
                    <button type="submit" class="vote-button">Перейти до голосування</button>
                </div>
            </form>
        </div>
    </section>
@endsection
