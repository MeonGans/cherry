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

        <div class="table-responsive">
            <table class="table-hover table">
                <thead>
                <tr>
                    <th>#</th>
                    <th class="w-20">Фото</th>
                    <th>Ім'я</th>
                    <th>Команда</th>
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
                        <td>{{ $user->team->name ?? '' }}</td>
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
                    <tr>
                        <td colspan="6" class="text-center text-white-dark">У цій сесії ще немає учнів.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
