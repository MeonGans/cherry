@extends('layouts.app2')

@section('content')
    <main class="nevermore-quest">
        <div class="nevermore-wrap">
            <section class="nevermore-panel">
                <div class="nevermore-inner">
                    <div class="nevermore-content">
                        <div class="nevermore-crest" aria-hidden="true">W</div>
                        <p class="nevermore-kicker">Nevermore case file</p>
                        <h1 class="nevermore-title">Код жертви</h1>
                        <p class="nevermore-copy">
                            Введіть головний код маршруту, щоб відкрити назву команди і продовжити розслідування.
                        </p>

                        @if($errors->any())
                            <div class="nevermore-alert">
                                @foreach($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <form action="{{ route('wednesday.quest.unlock') }}" method="POST" class="nevermore-form" autocomplete="off">
                            @csrf
                            <label for="victim_code" class="nevermore-label">Код жертви</label>
                            <input
                                type="text"
                                name="victim_code"
                                id="victim_code"
                                class="nevermore-input"
                                value="{{ old('victim_code') }}"
                                autofocus
                                required
                            >
                            <button type="submit" class="nevermore-button">Відкрити маршрут</button>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </main>

    @include('wednesday-quest._styles')
@endsection
