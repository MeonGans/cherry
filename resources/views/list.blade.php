@extends('layouts.app')
@section('content')
<div class="panel">
    <div class="mb-5 flex items-center justify-between">
        <h5 class="text-lg font-semibold dark:text-white-light">Список заїзду</h5>
        <!-- contextual -->
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
        <div class="table-responsive">
            <table class="table-hover table">
                <thead>
                <tr>
                    <th>#</th>
                    <th class="w-20">Фото</th>
                    <th>Ім'я</th>
{{--                    <th>Номер телефону</th>--}}
                    <th>Команда</th>
                    <th>PIN-код</th>
                    <th class="w-40 text-right">Дії</th>
                </tr>
                </thead>
                <tbody>
                @php
                    $i = 1;
                @endphp
                @foreach($users as $user)

                    <tr style="background-color: {{ $user->team->element->color ?? '#ffffff' }};">
                        <td>{{ $i }}</td>
                        <td>
                            <img
                                src="{{ $user->image_url }}"
                                alt="{{ $user->name }}"
                                class="h-12 w-12 rounded object-cover"
                            >
                        </td>
                        <td class="font-semibold">{{ $user->name }}</td>
{{--                        <td>{{ $user->phone_number }}</td>--}}
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
                    @php
                        $i++;
                    @endphp
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
