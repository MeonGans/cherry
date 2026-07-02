@extends('layouts.vote')

@section('title', 'Голос зараховано - CHERRY CAMP')

@section('content')
    <section class="vote-card">
        <div class="vote-card-inner">
            <span class="vote-kicker">Готово</span>
            <h1 class="vote-title">Ваш голос зараховано!</h1>
            <p class="vote-copy">Дякуємо за участь. Результати буде видно після завершення голосування.</p>
            <div class="vote-success" role="status">Голос успішно збережено.</div>
        </div>
    </section>
@endsection
