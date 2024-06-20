@extends('layouts.app')

@section('content')
    <div class="panel">
        <div class="mb-5 flex items-center justify-between">
            <h5 class="text-lg font-semibold dark:text-white-light">Тест розподілу за командами</h5>
            <!-- contextual -->
        </div>
        <div class="mb-5">
            <div class="table-responsive">
                <form action="{{ route('test.submit') }}" method="POST">
                    @csrf
                    <div class="mb-5">
                    <select class="selectize h-5" name="user_id" id="user_id" required>
                        <option selected value="">Виберіть користувача</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    </div>
                    @foreach($questions as $question)
                        @php
                            $answers = $question->answers->shuffle();
                        @endphp
                        <div class="mb-5">
                            <label>{{ $question->question }}</label>
                            <div class="flex">
                                <div class="mb-5">
                                    <div class="space-y-2">
                                        @foreach($answers as $answer)<div>

                                                <label class="inline-flex">
                                                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $answer->id }}" class="form-radio peer" required />
                                                    <span class="peer-checked:text-primary">{{ $answer->answer }}</span>
                                                </label>

                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <button type="submit" class="btn btn-primary mt-6">Відправити</button>
                </form>
            </div>
        </div>

    </div>
@endsection
