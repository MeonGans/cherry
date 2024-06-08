@extends('layouts.app')

@section('content')
    <div class="panel">
        <div class="mb-5 flex items-center justify-between">
            <h5 class="text-lg font-semibold dark:text-white-light">Список голосувань</h5>
            <!-- contextual -->
        </div>
        <div class="mb-5">
            <div class="table-responsive">
                <table>
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Назва</th>
                        <th>URL</th>
                        <th>Результат</th>
                        <th>Додати</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        $i = 1;
                    @endphp
                    @foreach($votes as $vote)

                        <tr style="background-color: {{ $user->team->element->color ?? '#ffffff' }};">
                            <td>{{ $i }}</td>
                            <td>{{ $vote->name }}</td>
                            <td><a href="{{ route('votes.show', $vote->vote_url) }}">{{ url('/votes/' . $vote->vote_url) }}</a></td>
                            <td><a href="{{ route('votes.result', $vote->vote_url) }}"><button type="button" class="btn btn-outline-primary">Результат</button></a></td>
                            <td><a href="{{ route('votes.addPointsForm', $vote->vote_url) }}"><button type="button" class="btn btn-outline-warning">Додати бали</button></a></td>
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
