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
            <div>
                <h5 class="text-lg font-semibold dark:text-white-light">Рандомний список заїзду</h5>
                <p class="text-sm text-white-dark print-hidden">Чистий список для друку без фото, PIN-кодів і редагування.</p>
            </div>
            <button type="button" class="btn btn-primary print-hidden" onclick="window.print()">
                Друк
            </button>
        </div>
        <div class="mb-5 print-hidden">
            <input
                type="search"
                class="form-input max-w-md"
                placeholder="Пошук за ім'ям або командою"
                data-table-search="#random-arrival-table"
            >
        </div>

        <div class="mb-5">
            <div class="table-responsive">
                <table id="random-arrival-table" class="table-hover table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Ім'я</th>
                        <th>Команда</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        $i = 1;
                    @endphp
                    @foreach($users as $user)

                        <tr style="background-color: {{ $user->team->element->color ?? '#ffffff' }};">
                            <td>{{ $i }}</td>
                            <td class="font-semibold">{{ $user->name }}</td>
                            <td>{{ $user->team->name ?? '' }}</td>
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
