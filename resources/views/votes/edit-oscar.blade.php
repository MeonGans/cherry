@extends('layouts.app')

@section('content')
    <div class="panel">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h5 class="text-lg font-semibold dark:text-white-light">Редагування номінантів</h5>
                <p class="mt-1 text-sm text-white-dark">{{ $vote->name }} · заїзд #{{ $vote->session_id }}</p>
            </div>
            <a href="{{ route('votes.index') }}" class="btn btn-outline-secondary">До голосувань</a>
        </div>

        @if(session('success'))
            <div class="mb-4 rounded border border-success bg-success/10 p-3 text-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="mb-4 rounded border border-danger bg-danger/10 p-3 text-danger">{{ $errors->first() }}</div>
        @endif

        <div class="mb-4 rounded border border-warning bg-warning/10 p-3 text-sm">
            Якщо вилучити номінанта, уже отримані ним голоси та бали в цій номінації також буде видалено.
        </div>

        <form action="{{ route('votes.oscar.update', $vote->vote_url) }}" method="POST">
            @csrf
            @method('PUT')
            @include('votes.partials.oscar-nominee-picker')
            <button type="submit" class="btn btn-primary mt-6">Зберегти номінантів</button>
        </form>
    </div>
@endsection
