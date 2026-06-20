@extends('layouts.app')

@section('content')
    <div class="panel">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h5 class="text-lg font-semibold dark:text-white-light">Редагувати маршрут</h5>
                <p class="text-sm text-white-dark">Можна змінити код жертви, назву команди і будь-яку підказку маршруту.</p>
            </div>
            <a href="{{ route('wednesday-quest-routes.index') }}" class="btn btn-outline-primary">До маршрутів</a>
        </div>

        @if($errors->any())
            <div class="mb-5 rounded border border-danger bg-danger-light p-3 text-danger">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @include('wednesday-quest-routes._form', [
            'questRoute' => $questRoute,
            'action' => route('wednesday-quest-routes.update', $questRoute),
            'method' => 'PUT',
        ])
    </div>
@endsection
