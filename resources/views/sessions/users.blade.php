@extends('layouts.app')

@section('content')
    <div class="panel">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h5 class="text-lg font-semibold dark:text-white-light">Учні сесії</h5>
                <p class="text-sm text-white-dark">
                    {{ \Illuminate\Support\Carbon::parse($session->start_date)->format('d.m.Y') }}
                    -
                    {{ \Illuminate\Support\Carbon::parse($session->end_date)->format('d.m.Y') }}
                    @if($session->active)
                        <span class="ml-2 badge bg-success">Активна</span>
                    @else
                        <span class="ml-2 badge bg-dark">Неактивна</span>
                    @endif
                </p>
            </div>
            <a href="{{ route('sessions.index') }}" class="btn btn-outline-primary">До сесій</a>
        </div>

        <div class="mb-4 rounded border border-white-light p-4 dark:border-[#1b2e4b]">
            <div class="text-sm text-white-dark">Кількість учнів у сесії</div>
            <div class="mt-1 text-xl font-semibold">{{ $users->count() }}</div>
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

        <form action="{{ route('sessions.users.store', $session) }}" method="POST" class="mb-5 rounded border border-white-light p-4 dark:border-[#1b2e4b]">
            @csrf
            <div class="mb-4">
                <h6 class="text-base font-semibold text-black dark:text-white">Додати учня</h6>
                <p class="text-sm text-white-dark">PIN-код сформується автоматично, фото можна додати пізніше через редагування.</p>
            </div>
            <div class="grid gap-4 md:grid-cols-[minmax(0,1fr)_220px_180px_auto]">
                <div>
                    <label for="name" class="mb-2 block font-semibold">Ім'я</label>
                    <input type="text" name="name" id="name" class="form-input" value="{{ old('name') }}" required>
                </div>
                <div>
                    <label for="desired_team_id" class="mb-2 block font-semibold">Очікувана команда</label>
                    <select name="desired_team_id" id="desired_team_id" class="form-select">
                        <option value="">Без команди</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}" {{ (string) old('desired_team_id') === (string) $team->id ? 'selected' : '' }}>
                                #{{ $team->id }} - {{ $team->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="gender" class="mb-2 block font-semibold">Стать</label>
                    <select name="gender" id="gender" class="form-select" required>
                        <option value="female" @selected(old('gender', 'female') === 'female')>Дівчина</option>
                        <option value="male" @selected(old('gender') === 'male')>Хлопець</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="btn btn-primary w-full">Додати</button>
                </div>
            </div>
        </form>

        <div class="mb-5">
            <input
                type="search"
                class="form-input max-w-md"
                placeholder="Пошук учня, команди або PIN"
                data-table-search="#session-users-table"
            >
        </div>

        <div class="table-responsive">
            <table id="session-users-table" class="table-hover table">
                <thead>
                <tr>
                    <th>#</th>
                    <th class="w-20">Фото</th>
                    <th>Ім'я</th>
                    <th>Стать</th>
                    <th>Команда</th>
                    <th>Очікувана команда</th>
                    <th>PIN-код</th>
                    <th class="w-40 text-right">Дії</th>
                </tr>
                </thead>
                <tbody>
                @forelse($users as $index => $user)
                    <tr style="background-color: {{ $user->team->element->color ?? '#ffffff' }};">
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <img
                                src="{{ $user->image_url }}"
                                alt="{{ $user->name }}"
                                class="h-12 w-12 rounded object-cover"
                            >
                        </td>
                        <td class="font-semibold">{{ $user->name }}</td>
                        <td>{{ $user->gender === 'male' ? 'Хлопець' : 'Дівчина' }}</td>
                        <td>{{ $user->team->name ?? '' }}</td>
                        <td>{{ $user->desiredTeam->name ?? '—' }}</td>
                        <td>{{ $user->pin_code ?? '—' }}</td>
                        <td>
                            <div class="flex justify-end">
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-primary btn-sm">
                                    Редагувати
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr data-empty-row>
                        <td colspan="8" class="text-center text-white-dark">У цій сесії ще немає учнів.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
