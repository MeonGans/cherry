@extends('layouts.app')

@section('content')
    <div class="panel">
        <div class="mb-5 flex items-center justify-between">
            <h5 class="text-lg font-semibold dark:text-white-light">Додати бали журі: {{ $vote->name }}</h5>
            <a href="{{ route('votes.result', $vote->vote_url) }}" class="btn btn-outline-primary">Результат</a>
        </div>

        @if($errors->any())
            <div class="mb-4 rounded border border-danger bg-danger/10 p-3 text-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="grid gap-5">
            @foreach($nominations as $key => $nomination)
                @php
                    $candidates = $candidatesByNomination[$key] ?? collect();
                @endphp

                <section class="rounded border border-white-light p-4 dark:border-[#191e3a]">
                    <h6 class="mb-4 text-base font-bold">{{ $nomination['title'] }}</h6>

                    @if($candidates->isEmpty())
                        <p class="text-white-dark">У цій номінації немає номінантів.</p>
                    @else
                        <form action="{{ route('votes.addPoints', $vote->vote_url) }}" method="POST" class="grid gap-4 md:grid-cols-[1fr_160px_auto] md:items-end">
                            @csrf
                            <input type="hidden" name="nomination" value="{{ $key }}">

                            <div>
                                <label for="nominee_user_id_{{ $key }}">Номінант</label>
                                <select id="nominee_user_id_{{ $key }}" name="nominee_user_id" class="form-select" required>
                                    @foreach($candidates as $candidate)
                                        <option value="{{ $candidate->id }}">{{ $candidate->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="points_{{ $key }}">Бали</label>
                                <input type="number" id="points_{{ $key }}" name="points" class="form-input" min="1" required>
                            </div>

                            <button type="submit" class="btn btn-primary">Додати</button>
                        </form>

                        <div class="mt-4 grid grid-cols-2 gap-3 md:grid-cols-6">
                            @foreach($candidates as $candidate)
                                <div class="overflow-hidden rounded border border-white-light dark:border-[#191e3a]">
                                    <img src="{{ $candidate->image_url }}" alt="{{ $candidate->name }}" class="h-28 w-full object-cover">
                                    <div class="p-2 text-center text-xs font-semibold">{{ $candidate->name }}</div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>
            @endforeach
        </div>
    </div>
@endsection
