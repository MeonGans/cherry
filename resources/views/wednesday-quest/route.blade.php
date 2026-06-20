@extends('layouts.app2')

@section('content')
    <main class="nevermore-quest">
        <div class="nevermore-wrap">
            <section class="nevermore-panel">
                <div class="nevermore-inner">
                    <div class="nevermore-content">
                        <a href="{{ route('wednesday.quest.index') }}" class="route-back">Змінити код жертви</a>

                        <p class="nevermore-kicker">Команда</p>
                        <h1 class="team-title">{{ $questRoute->name }}</h1>
                        <p class="nevermore-copy">
                            Вітаємо. Маршрут активовано. Вводьте наступні коди, щоб відкривати підказки цієї команди.
                        </p>

                        @if($errors->any())
                            <div class="nevermore-alert">
                                @foreach($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <div class="route-grid">
                            <section class="route-card">
                                @if($isFinal)
                                    <div class="hint-card safe-panel">
                                        <p class="hint-number">Фінальний код прийнято</p>
                                        <p class="hint-text">Архів відкрив шлях до сейфу.</p>
                                        <a href="{{ route('quest.show') }}" class="nevermore-safe-button">Перейти до сейфу</a>
                                    </div>
                                @elseif($hintNumber)
                                    <div class="hint-card">
                                        <p class="hint-number">Підказка {{ $hintNumber }}</p>
                                        <p class="hint-text">{{ $hintText }}</p>
                                    </div>
                                @else
                                    <div class="hint-card">
                                        <p class="hint-placeholder">
                                            Архів очікує наступний код. Якщо він правильний, справа відкриється нова підказка.
                                        </p>
                                    </div>
                                @endif
                            </section>

                            <section class="route-card">
                                <form action="{{ route('wednesday.quest.hint', $questRoute) }}" method="POST" class="nevermore-form" autocomplete="off">
                                    @csrf
                                    <label for="step_code" class="nevermore-label">Наступний код</label>
                                    <input
                                        type="text"
                                        name="step_code"
                                        id="step_code"
                                        class="nevermore-input"
                                        value="{{ old('step_code') }}"
                                        inputmode="numeric"
                                        autofocus
                                        required
                                    >
                                    <button type="submit" class="nevermore-button">Отримати підказку</button>
                                </form>
                            </section>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    @include('wednesday-quest._styles')
@endsection
