@extends('layouts.app')

@section('content')
    <div class="panel">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h5 class="text-lg font-semibold dark:text-white-light">Сесії</h5>
                <p class="text-sm text-white-dark">Керуйте активною сесією та переглядайте учнів кожного заїзду.</p>
            </div>
            <a href="{{ route('sessions.create') }}" class="btn btn-primary">Створити сесію</a>
        </div>

        @if(session('success'))
            <div class="mb-5 rounded border border-success bg-success-light p-3 text-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-5 rounded border border-danger bg-danger-light p-3 text-danger">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mb-5">
            <input
                type="search"
                class="form-input max-w-md"
                placeholder="Пошук за датою, статусом або кількістю учнів"
                data-table-search="#sessions-table"
            >
        </div>

        <div class="table-responsive">
            <table id="sessions-table" class="table-hover table">
                <thead>
                <tr>
                    <th class="w-20">#</th>
                    <th>Період</th>
                    <th class="w-40">Статус</th>
                    <th class="w-40">Учнів</th>
                    <th class="w-80 text-right">Дії</th>
                </tr>
                </thead>
                <tbody>
                @forelse($sessions as $session)
                    <tr data-empty-row>
                        <td>{{ $session->id }}</td>
                        <td>
                            <div class="font-semibold text-black dark:text-white">
                                {{ \Illuminate\Support\Carbon::parse($session->start_date)->format('d.m.Y') }}
                                -
                                {{ \Illuminate\Support\Carbon::parse($session->end_date)->format('d.m.Y') }}
                            </div>
                            <div class="text-xs text-white-dark">
                                Створено: {{ $session->created_at?->format('d.m.Y H:i') }}
                            </div>
                        </td>
                        <td>
                            @if($session->active)
                                <span class="badge bg-success">Активна</span>
                            @else
                                <span class="badge bg-dark">Неактивна</span>
                            @endif
                        </td>
                        <td>{{ $session->users_count }}</td>
                        <td>
                            <div class="flex flex-wrap justify-end gap-2">
                                <a href="{{ route('sessions.users', $session) }}" class="btn btn-outline-primary btn-sm">
                                    Учні
                                </a>

                                @if($session->active)
                                    <form action="{{ route('sessions.deactivate', $session) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-outline-warning btn-sm">
                                            Деактивувати
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('sessions.activate', $session) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success btn-sm">
                                            Активувати
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-white-dark">Сесій ще немає.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
