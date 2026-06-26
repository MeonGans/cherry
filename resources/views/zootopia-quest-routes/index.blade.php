@extends('layouts.app')

@section('content')
    <div class="panel">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h5 class="text-lg font-semibold dark:text-white-light">Маршрути квесту "Зоотрополіс: мисливці за доказами"</h5>
                <p class="text-sm text-white-dark">Створюйте окремі маршрути з власним кодом агента та підказками 2-9.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('zootopia.quest.index') }}" class="btn btn-outline-primary" target="_blank">Відкрити квест</a>
                <a href="{{ route('zootopia-quest-routes.create') }}" class="btn btn-primary">Створити маршрут</a>
            </div>
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

        <div class="table-responsive">
            <table class="table-hover table">
                <thead>
                <tr>
                    <th class="w-20">#</th>
                    <th>Маршрут</th>
                    <th class="w-48">Код агента</th>
                    <th>Перша системна підказка</th>
                    <th class="w-44">Оновлено</th>
                    <th class="w-64 text-right">Дії</th>
                </tr>
                </thead>
                <tbody>
                @forelse($questRoutes as $questRoute)
                    <tr>
                        <td>{{ $questRoute->id }}</td>
                        <td>
                            <div class="font-semibold text-black dark:text-white">{{ $questRoute->name }}</div>
                            <div class="text-xs text-white-dark">Підказки: 2-9, фінальний код: шмиг</div>
                        </td>
                        <td>
                            <span class="rounded bg-dark px-2 py-1 font-mono text-white dark:bg-white-light dark:text-black">
                                {{ $questRoute->agent_code }}
                            </span>
                        </td>
                        <td class="text-white-dark">
                            {{ \Illuminate\Support\Str::limit($questRoute->hint_2, 90) }}
                        </td>
                        <td>{{ $questRoute->updated_at?->format('d.m.Y H:i') }}</td>
                        <td>
                            <div class="flex flex-wrap justify-end gap-2">
                                <a href="{{ route('zootopia.quest.route', $questRoute) }}" class="btn btn-outline-info btn-sm" target="_blank">
                                    Переглянути
                                </a>
                                <a href="{{ route('zootopia-quest-routes.edit', $questRoute) }}" class="btn btn-outline-primary btn-sm">
                                    Редагувати
                                </a>
                                <form action="{{ route('zootopia-quest-routes.destroy', $questRoute) }}" method="POST" onsubmit="return confirm('Видалити цей маршрут?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">Видалити</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-white-dark">Маршрутів ще немає.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
