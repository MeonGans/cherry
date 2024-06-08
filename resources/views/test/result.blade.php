@extends('layouts.app')

@section('content')

    <div class="panel">
        <div class="mb-5 flex items-center justify-between">
            <img class="w-[60.55%] rounded-full overflow-hidden object-cover" src="{{ asset('assets/images/elements/'.$team->id.'.png') }}" alt="image" />
        </div>
    </div>

    <div class="panel">
        <div class="mb-5 flex items-center justify-between">
    <h1>Ваша команда: {{ $team->name }}</h1>
        </div>
    </div>
@endsection
