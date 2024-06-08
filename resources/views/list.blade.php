@extends('layouts.app')
@section('content')
<div class="panel">
    <div class="mb-5 flex items-center justify-between">
        <h5 class="text-lg font-semibold dark:text-white-light">Список заїзду</h5>
        <!-- contextual -->
    </div>
    <div class="mb-5">
        <div class="table-responsive">
            <table>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Ім'я</th>
                    <th>Номер телефону</th>
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
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->phone_number }}</td>
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
