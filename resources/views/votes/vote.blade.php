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

        <form action="{{ route('votes.submitVote', ['voteUrl' => $vote->vote_url, 'userId' => $user->id]) }}" method="POST">
            @csrf
            <div>
                <label for="team_id">Оберіть команду</label>
                <select class="selectize" id="team_id" name="team_id" required>
                    <option selected value="">Виберіть команду</option>
                    @foreach($teams as $team)
                        <option value="{{ $team->id }}">{{ $team->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary mt-6">Проголосувати</button>
        </form>
    </div>
@endsection
