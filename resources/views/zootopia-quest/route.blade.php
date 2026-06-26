@extends('layouts.app2')

@section('content')
    <main class="zootopia-quest zootopia-quest--route">
        <section class="zootopia-hero" aria-label="Зоотрополіс"></section>

        <section class="zootopia-console" aria-label="База даних сервера Зоотрополіса">
            <header class="zootopia-header">
                <div class="zootopia-brand">
                    <div class="zootopia-badge" aria-hidden="true">ZPD</div>
                    <div>
                        <p class="zootopia-kicker">Zootropolis secure network</p>
                        <h1>База даних сервера Зоотрополіса</h1>
                    </div>
                </div>

                <div class="zootopia-status">
                    <span aria-hidden="true"></span>
                    Online
                </div>
            </header>

            <div class="zootopia-meta-grid">
                <section class="zootopia-meta">
                    <p>Команда</p>
                    <strong>{{ $questRoute->name }}</strong>
                </section>
                <section class="zootopia-meta">
                    <p>Поточний етап</p>
                    <strong>
                        @if($isFinal)
                            Доступ до скрині
                        @elseif($hintNumber)
                            Підказка {{ $hintNumber }}
                        @else
                            Введення коду
                        @endif
                    </strong>
                </section>
            </div>

            @if($errors->any())
                <div class="zootopia-alert">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <section class="zootopia-terminal" aria-live="polite">
                @if($isFinal)
                    <div class="zootopia-final">
                        <p class="zootopia-label">Фінальний код прийнято</p>
                        <h2>Доступ до скрині відкрито.</h2>
                        <a href="{{ route('zootopia.safe.show') }}" class="zootopia-safe-link">Перейти до скрині</a>
                    </div>
                @elseif($hintNumber)
                    <div class="zootopia-evidence">
                        <p class="zootopia-label">Доказ #{{ $hintNumber }}</p>
                        <div class="zootopia-hint">{{ $hintText }}</div>
                    </div>
                @else
                    <div class="zootopia-line">Маршрут активовано.</div>
                    <div class="zootopia-line">Введіть наступний код, щоб відкрити нову підказку цієї команди.</div>
                @endif
            </section>

            <form action="{{ route('zootopia.quest.hint', $questRoute) }}" method="POST" class="zootopia-command" autocomplete="off">
                @csrf
                <label for="step_code" class="zootopia-sr">Наступний код</label>
                <div class="zootopia-input-wrap">
                    <span aria-hidden="true">#</span>
                    <input
                        type="text"
                        name="step_code"
                        id="step_code"
                        value="{{ old('step_code') }}"
                        placeholder="Введіть код або пароль..."
                        autofocus
                        required
                    >
                </div>
                <button type="submit" class="zootopia-primary">Підтвердити</button>
            </form>

            <div class="zootopia-actions">
                <a href="{{ route('zootopia.quest.index') }}" class="zootopia-change">Змінити код</a>
            </div>
        </section>
    </main>

    @include('zootopia-quest._styles')
@endsection
