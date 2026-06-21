@extends('layouts.users.app')

@section('content')
    <div class="panel">
        <div class="mb-5 flex items-center justify-between">
            <h5 class="text-lg font-semibold dark:text-white-light">{{ $vote->name }}</h5>
        </div>

        @if($errors->any())
            <div class="mb-4 rounded border border-danger bg-danger/10 p-3 text-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('votes.authenticate', $vote->vote_url) }}" method="POST">
            @csrf
            <div>
                <label for="pin_code">Уведіть ваш PIN</label>
                <input type="text" id="pin_code" name="pin_code" class="form-input" required>
            </div>
            <button type="submit" class="btn btn-primary mt-6">Перейти до голосування</button>
        </form>
    </div>
@endsection
