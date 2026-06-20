@php
    $isEdit = $questRoute->exists;
    $hintCodes = \App\Models\WednesdayQuestRoute::HINT_CODES;
@endphp

<form action="{{ $action }}" method="POST" class="grid gap-5">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label for="name" class="mb-2 block font-semibold">Назва маршруту / команда</label>
            <input
                type="text"
                name="name"
                id="name"
                class="form-input"
                value="{{ old('name', $questRoute->name) }}"
                placeholder="Наприклад: Команда Мортиші"
                required
            >
        </div>

        <div>
            <label for="victim_code" class="mb-2 block font-semibold">Код жертви</label>
            <input
                type="text"
                name="victim_code"
                id="victim_code"
                class="form-input"
                value="{{ old('victim_code', $questRoute->victim_code) }}"
                placeholder="Головний пін-код маршруту"
                required
            >
        </div>
    </div>

    <div class="rounded border border-white-light p-4 dark:border-[#1b2e4b]">
        <div class="mb-4">
            <h6 class="font-semibold text-black dark:text-white">Підказки маршруту</h6>
            <p class="text-sm text-white-dark">Перша підказка видається на руки, тут заповнюються підказки від 2 до 9. Код 888 веде до сейфу.</p>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            @foreach(\App\Models\WednesdayQuestRoute::HINT_NUMBERS as $number)
                <div>
                    <label for="hint_{{ $number }}" class="mb-2 block font-semibold">
                        Підказка {{ $number }} <span class="text-xs font-normal text-white-dark">(код {{ $hintCodes[$number] }})</span>
                    </label>
                    <textarea
                        name="hint_{{ $number }}"
                        id="hint_{{ $number }}"
                        rows="4"
                        class="form-textarea"
                        required
                    >{{ old("hint_{$number}", $questRoute->getAttribute("hint_{$number}")) }}</textarea>
                </div>
            @endforeach
        </div>
    </div>

    <div class="flex flex-wrap gap-2">
        <button type="submit" class="btn btn-primary">
            {{ $isEdit ? 'Зберегти маршрут' : 'Створити маршрут' }}
        </button>
        <a href="{{ route('wednesday-quest-routes.index') }}" class="btn btn-outline-danger">Скасувати</a>
    </div>
</form>
