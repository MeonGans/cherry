@extends('layouts.app2')

@section('content')
    <main class="zootopia-quest zootopia-quest--entry">
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
                    <p>Вітаємо</p>
                    <strong>Агент не авторизований</strong>
                </section>
                <section class="zootopia-meta">
                    <p>Поточний етап</p>
                    <strong>Введення коду</strong>
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
                <div class="zootopia-line">Вітаємо, агенте!</div>
                <div class="zootopia-line">Введіть свій код, щоб увійти до системи.</div>
            </section>

            <form action="{{ route('zootopia.quest.unlock') }}" method="POST" class="zootopia-command" autocomplete="off">
                @csrf
                <label for="agent_code" class="zootopia-sr">Код агента</label>
                <div class="zootopia-input-wrap">
                    <span aria-hidden="true">#</span>
                    <input
                        type="text"
                        name="agent_code"
                        id="agent_code"
                        value="{{ old('agent_code') }}"
                        placeholder="Введіть код або пароль..."
                        autofocus
                        required
                    >
                </div>
                <button type="submit" class="zootopia-primary">Підтвердити</button>
            </form>
        </section>
    </main>

    @include('zootopia-quest._styles')
@endsection
