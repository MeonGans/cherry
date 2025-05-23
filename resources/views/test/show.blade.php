@extends('layouts.app')

@section('content')
    <div class="panel">
        <div class="mb-5 flex items-center justify-between">
            <h5 class="text-lg font-semibold dark:text-white-light">Тест розподілу за командами</h5>
            <!-- contextual -->
        </div>
        <div class="mb-5">
            <div class="table-responsive">
                <form action="{{ route('test.handle') }}" method="POST">
                    @csrf
                    <div class="mb-5">
                    <select class="selectize h-5" name="user_id" id="user_id" required>
                        <option selected value="">Виберіть користувача</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    </div>
                    @foreach ($questions as $question)
                        @php  $answers = $question->answers->shuffle();  @endphp

                        <div class="mb-8">
                            <label class="block mb-4 font-medium">{{ $question->question }}</label>

                            {{-- варіанти картинками по 3 в ряд --}}
                            <div class="grid grid-cols-3 gap-4">
                                @foreach ($answers as $answer)
                                    <label class="relative cursor-pointer">
                                        {{-- прихований radio --}}
                                        <input  type="radio"
                                                name="answers[{{ $question->id }}]"
                                                value="{{ $answer->id }}"
                                                class="sr-only peer"
                                                required />

                                        {{-- зображення відповіді --}}
                                        <img    src="assets/images/answer/{{ $answer->img }}.jpg"
                                                alt="{{ $answer->answer }}"
                                                class="w-full aspect-square object-cover rounded-lg
                                                       border-4 border-transparent
                                                       transition-all
                                                       peer-checked:border-primary
                                                       peer-checked:ring-4 peer-checked:ring-primary/30" />

                                        {{-- галочка поверх вибраної (можна прибрати) --}}
                                        {{-- ✅ галочка поверх (оновлена) --}}
                                        <svg viewBox="0 0 24 24"
                                             class="absolute inset-0 m-auto w-12 h-12
                stroke-white drop-shadow-lg   {{-- біла обводка + легка тінь --}}
                opacity-0 scale-90 z-10      {{-- поверх фото --}}
                transition duration-200
                pointer-events-none
                peer-checked:opacity-100 peer-checked:scale-100">
                                            <path d="M5 13l4 4L19 7"
                                                  fill="none"
                                                  stroke-width="3"
                                                  stroke-linecap="round"
                                                  stroke-linejoin="round" />
                                        </svg>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                    <button type="submit" class="btn btn-primary mt-6">Відправити</button>
                </form>
            </div>
        </div>

    </div>
@endsection
