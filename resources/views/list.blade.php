@extends('layouts.app')
@section('content')
<style>
    @media print {
        @page {
            margin: 14mm;
        }

        body {
            background: #ffffff !important;
            color: #000000 !important;
        }

        .sidebar,
        .screen_loader,
        .print-hidden,
        .main-content > header,
        .fixed,
        script {
            display: none !important;
        }

        .main-container,
        .main-content,
        .dvanimation,
        .panel {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            box-shadow: none !important;
            border: 0 !important;
            background: #ffffff !important;
        }

        .table-responsive {
            overflow: visible !important;
        }

        table {
            width: 100% !important;
            border-collapse: collapse !important;
            font-size: 13px;
        }

        th,
        td {
            border: 1px solid #000000 !important;
            padding: 7px 8px !important;
            color: #000000 !important;
            background: transparent !important;
        }

        th {
            font-weight: 700 !important;
        }

        tr {
            break-inside: avoid;
        }
    }
</style>

<div class="panel">
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <h5 class="text-lg font-semibold dark:text-white-light">Список заїзду</h5>
        <button type="button" class="btn btn-primary print-hidden" onclick="window.print()">
            Друк
        </button>
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

    <div class="mb-5 print-hidden">
        <input
            type="search"
            class="form-input max-w-md"
            placeholder="Пошук за ім'ям, командою або PIN"
            data-table-search="#arrival-table"
        >
    </div>

    <div class="mb-5">
        <div class="table-responsive">
            <table id="arrival-table" class="table-hover table">
                <thead>
                <tr>
                    <th class="print-hidden">#</th>
                    <th class="w-20 print-hidden">Фото</th>
                    <th>Ім'я</th>
{{--                    <th>Номер телефону</th>--}}
                    <th>Команда</th>
                    <th>PIN-код</th>
                    <th class="w-40 text-right print-hidden">Дії</th>
                </tr>
                </thead>
                <tbody>
                @php
                    $i = 1;
                @endphp
                @foreach($users as $user)

                    <tr style="background-color: {{ $user->team->element->color ?? '#ffffff' }};">
                        <td class="print-hidden">{{ $i }}</td>
                        <td class="print-hidden">
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
                        <td class="print-hidden">
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
