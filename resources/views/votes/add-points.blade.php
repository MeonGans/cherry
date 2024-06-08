@extends('layouts.users.app')

@section('content')
    <div class="panel">
        <div class="mb-5 flex items-center justify-between">
            <h5 class="text-lg font-semibold dark:text-white-light">Додати бали для голосування: {{ $vote->name }}</h5>
            <!-- contextual -->
        </div>
        <div class="mb-5">
            <form action="{{ route('votes.addPoints', $vote->vote_url) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="team_id">Команда</label>
                    <select class="selectize" name="team_id" id="team_id" class="form-control">
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="points">Бали</label>
                    <input type="text" id="points" name="points" class="form-input" required>
                </div>
                <div class="form-group mt-2">
                <button type="submit" class="btn btn-primary">Додати бали</button>
                </div>
            </form>
        </div>
    </div>
@endsection

