@extends('layouts.app2')

@section('content')
    @php
        $elementStyles = [
            'fire' => ['name' => 'Вогонь', 'color' => '#f97316'],
            'air' => ['name' => 'Повітря', 'color' => '#38bdf8'],
            'water' => ['name' => 'Вода', 'color' => '#0ea5e9'],
            'earth' => ['name' => 'Земля', 'color' => '#22c55e'],
        ];
    @endphp

    <style>
        @media (min-width: 1024px) {
            body:has(.element-sort) .main-container .main-content {
                margin-left: 0 !important;
                margin-right: 0 !important;
            }
        }

        .element-sort {
            min-height: calc(100vh - 48px);
            margin: -1.5rem;
            padding: clamp(16px, 3vw, 34px);
            color: #142033;
            background:
                linear-gradient(135deg, rgba(255, 247, 237, 0.96), rgba(236, 253, 245, 0.92) 34%, rgba(224, 242, 254, 0.94) 68%, rgba(239, 246, 255, 0.96)),
                url("{{ asset('assets/images/knowledge/pattern.png') }}");
            background-size: cover, 420px;
            font-family: Nunito, Arial, sans-serif;
        }

        .element-sort-shell {
            width: min(1320px, 100%);
            margin: 0 auto;
            display: grid;
            grid-template-columns: minmax(250px, 340px) minmax(0, 1fr);
            gap: clamp(16px, 2vw, 24px);
        }

        .element-sort-side,
        .element-sort-stage {
            border: 1px solid rgba(15, 23, 42, 0.1);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 18px 48px rgba(15, 23, 42, 0.12);
            backdrop-filter: blur(10px);
        }

        .element-sort-side {
            align-self: start;
            padding: clamp(18px, 2.2vw, 26px);
        }

        .element-sort-stage {
            min-height: 680px;
            padding: clamp(18px, 2.4vw, 30px);
            overflow: hidden;
        }

        .element-sort-title {
            margin: 0;
            color: #0f172a;
            font-size: clamp(2rem, 4vw, 3.3rem);
            font-weight: 900;
            line-height: 0.98;
        }

        .element-sort-subtitle {
            margin: 14px 0 0;
            color: #475569;
            font-size: 1rem;
            font-weight: 700;
            line-height: 1.6;
        }

        .element-sort-runes {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-top: 22px;
        }

        .element-sort-rune {
            min-height: 86px;
            display: grid;
            place-items: center;
            border: 1px solid color-mix(in srgb, var(--element-color) 38%, transparent);
            border-radius: 8px;
            background:
                linear-gradient(135deg, color-mix(in srgb, var(--element-color) 18%, white), #ffffff);
            color: color-mix(in srgb, var(--element-color) 82%, #0f172a);
            font-size: 0.86rem;
            font-weight: 900;
            text-align: center;
        }

        .element-sort-rune img {
            width: 48px;
            height: 48px;
            object-fit: contain;
            margin-bottom: 6px;
        }

        .element-sort-field {
            margin-top: 24px;
        }

        .element-sort-label {
            display: block;
            margin-bottom: 8px;
            color: #334155;
            font-size: 0.9rem;
            font-weight: 900;
        }

        .element-sort-select {
            width: 100%;
            min-height: 48px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #ffffff;
            color: #0f172a;
            font-size: 1rem;
            font-weight: 800;
            outline: none;
            padding: 0 14px;
        }

        .element-sort-select:focus {
            border-color: #0ea5e9;
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.14);
        }

        .element-sort-empty,
        .element-sort-warning {
            margin-top: 18px;
            border-radius: 8px;
            background: #fff7ed;
            color: #9a3412;
            font-weight: 800;
            line-height: 1.45;
            padding: 14px;
        }

        .element-sort-progress {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 16px;
            align-items: center;
            margin-bottom: 24px;
        }

        .element-sort-track {
            height: 12px;
            overflow: hidden;
            border-radius: 8px;
            background: #e2e8f0;
        }

        .element-sort-bar {
            width: 25%;
            height: 100%;
            border-radius: 8px;
            background: linear-gradient(90deg, #f97316, #38bdf8, #0ea5e9, #22c55e);
            transition: width 220ms ease;
        }

        .element-sort-count {
            min-width: 74px;
            color: #475569;
            font-weight: 900;
            text-align: right;
        }

        .element-question {
            display: none;
            animation: elementQuestionIn 180ms ease-out;
        }

        .element-question.is-active {
            display: block;
        }

        .element-question-kicker {
            display: inline-flex;
            align-items: center;
            min-height: 34px;
            border-radius: 8px;
            background: #eff6ff;
            color: #1d4ed8;
            font-weight: 900;
            padding: 0 12px;
        }

        .element-question-title {
            max-width: 860px;
            margin: 18px 0 0;
            color: #111827;
            font-size: clamp(1.55rem, 3.3vw, 2.45rem);
            font-weight: 900;
            line-height: 1.12;
        }

        .element-answer-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: clamp(12px, 1.6vw, 18px);
            margin-top: 28px;
        }

        .element-answer {
            position: relative;
            display: block;
            min-width: 0;
            cursor: pointer;
        }

        .element-answer input {
            position: absolute;
            inset: 0;
            opacity: 0;
        }

        .element-answer-card {
            position: relative;
            min-height: 230px;
            overflow: hidden;
            border: 2px solid transparent;
            border-radius: 8px;
            background: #0f172a;
            box-shadow: 0 16px 36px rgba(15, 23, 42, 0.14);
            transition: transform 170ms ease, border-color 170ms ease, box-shadow 170ms ease;
        }

        .element-answer-card::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(180deg, transparent 26%, rgba(15, 23, 42, 0.86)),
                linear-gradient(135deg, color-mix(in srgb, var(--answer-color) 36%, transparent), transparent 48%);
            z-index: 1;
        }

        .element-answer-card img {
            width: 100%;
            height: 100%;
            min-height: 230px;
            aspect-ratio: 16 / 10;
            object-fit: cover;
            filter: saturate(1.08) contrast(1.04);
            transform: scale(1.01);
            transition: transform 180ms ease, filter 180ms ease;
        }

        .element-answer-copy {
            position: absolute;
            left: 16px;
            right: 16px;
            bottom: 14px;
            z-index: 2;
            display: grid;
            grid-template-columns: 42px 1fr;
            gap: 12px;
            align-items: center;
            color: #ffffff;
            text-align: left;
        }

        .element-answer-mark {
            display: grid;
            place-items: center;
            width: 42px;
            height: 42px;
            border-radius: 8px;
            background: color-mix(in srgb, var(--answer-color) 86%, #ffffff);
            color: #0f172a;
            font-weight: 1000;
        }

        .element-answer-text {
            min-width: 0;
            font-size: clamp(1rem, 1.8vw, 1.22rem);
            font-weight: 900;
            line-height: 1.2;
            overflow-wrap: anywhere;
            text-shadow: 0 2px 14px rgba(0, 0, 0, 0.34);
        }

        .element-answer:hover .element-answer-card {
            transform: translateY(-3px);
            box-shadow: 0 22px 42px rgba(15, 23, 42, 0.18);
        }

        .element-answer:hover img,
        .element-answer input:checked + .element-answer-card img {
            transform: scale(1.05);
            filter: saturate(1.2) contrast(1.08);
        }

        .element-answer input:checked + .element-answer-card,
        .element-answer input:focus-visible + .element-answer-card {
            border-color: color-mix(in srgb, var(--answer-color) 78%, #ffffff);
            box-shadow:
                0 22px 46px color-mix(in srgb, var(--answer-color) 24%, transparent),
                0 0 0 5px color-mix(in srgb, var(--answer-color) 16%, transparent);
        }

        .element-answer-check {
            position: absolute;
            top: 14px;
            right: 14px;
            z-index: 2;
            display: grid;
            place-items: center;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.92);
            color: #0f172a;
            font-weight: 1000;
            opacity: 0;
            transform: scale(0.82);
            transition: opacity 160ms ease, transform 160ms ease;
        }

        .element-answer input:checked + .element-answer-card .element-answer-check {
            opacity: 1;
            transform: scale(1);
        }

        .element-question.needs-answer .element-answer-grid,
        .element-sort-stage.needs-user .element-sort-progress {
            animation: elementShake 220ms ease;
        }

        .element-sort-actions {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-top: 28px;
        }

        .element-sort-button {
            min-width: 138px;
            min-height: 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid transparent;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 900;
            padding: 0 18px;
            transition: transform 160ms ease, box-shadow 160ms ease, opacity 160ms ease;
        }

        .element-sort-button:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.12);
        }

        .element-sort-button:disabled {
            cursor: not-allowed;
            opacity: 0.45;
        }

        .element-sort-button.secondary {
            border-color: #cbd5e1;
            background: #ffffff;
            color: #334155;
        }

        .element-sort-button.primary {
            background: #0f172a;
            color: #ffffff;
        }

        .element-sort-button.finish {
            display: none;
            background: linear-gradient(135deg, #f97316, #0ea5e9 58%, #22c55e);
            color: #ffffff;
        }

        .element-sort-button.finish.is-visible {
            display: inline-flex;
        }

        .element-reveal {
            display: none;
            min-height: 420px;
            place-items: center;
            text-align: center;
        }

        .element-reveal.is-visible {
            display: grid;
        }

        .element-reveal-ring {
            width: min(72vw, 320px);
            aspect-ratio: 1;
            display: grid;
            place-items: center;
            border-radius: 50%;
            background:
                conic-gradient(from 0deg, #f97316, #38bdf8, #0ea5e9, #22c55e, #f97316);
            box-shadow: 0 24px 70px rgba(15, 23, 42, 0.22);
            animation: elementSpin 1.25s linear infinite;
        }

        .element-reveal-core {
            width: 72%;
            aspect-ratio: 1;
            display: grid;
            place-items: center;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.94);
            color: #0f172a;
            font-size: clamp(1.2rem, 3vw, 1.8rem);
            font-weight: 1000;
            padding: 22px;
        }

        @keyframes elementQuestionIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes elementShake {
            0%, 100% {
                transform: translateX(0);
            }
            35% {
                transform: translateX(-4px);
            }
            70% {
                transform: translateX(4px);
            }
        }

        @keyframes elementSpin {
            to {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 980px) {
            .element-sort-shell {
                grid-template-columns: 1fr;
            }

            .element-sort-stage {
                min-height: 0;
            }
        }

        @media (max-width: 680px) {
            .element-sort {
                padding: 14px;
            }

            .element-answer-grid,
            .element-sort-runes {
                grid-template-columns: 1fr;
            }

            .element-answer-card,
            .element-answer-card img {
                min-height: 190px;
            }

            .element-sort-actions {
                flex-direction: column-reverse;
            }

            .element-sort-button {
                width: 100%;
            }
        }
    </style>

    <div class="element-sort" data-element-sort>
        <div class="element-sort-shell">
            <aside class="element-sort-side">
                <h1 class="element-sort-title">Сортування стихій</h1>
                <p class="element-sort-subtitle">Кожен вибір додає іскру, подих, хвилю або корінь до майбутньої команди.</p>

                <div class="element-sort-runes" aria-hidden="true">
                    @foreach($elementStyles as $index => $element)
                        <div class="element-sort-rune" style="--element-color: {{ $element['color'] }}">
                            <img src="{{ asset('assets/images/elements/' . ($loop->iteration) . '.png') }}" alt="">
                            <span>{{ $element['name'] }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="element-sort-field">
                    <label class="element-sort-label" for="user_id">Учасник</label>
                    <select class="element-sort-select" name="user_id" id="user_id" form="element_sort_form" required>
                        <option selected value="">Оберіть ім'я</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                @if($users->isEmpty())
                    <div class="element-sort-empty">Немає учнів, готових до сортування в активній сесії.</div>
                @endif

                <div class="element-sort-warning" data-user-warning hidden>Спершу оберіть учасника.</div>
            </aside>

            <main class="element-sort-stage">
                <form action="{{ route('test.handle') }}" method="POST" id="element_sort_form">
                    @csrf

                    <div class="element-sort-progress" aria-live="polite">
                        <div class="element-sort-track">
                            <div class="element-sort-bar" data-progress-bar></div>
                        </div>
                        <div class="element-sort-count" data-progress-text>1 / {{ count($questions) }}</div>
                    </div>

                    <div data-question-list>
                        @foreach ($questions as $questionIndex => $question)
                            @php
                                $answers = $question->answers->shuffle()->values();
                            @endphp

                            <section
                                class="element-question {{ $questionIndex === 0 ? 'is-active' : '' }}"
                                data-question
                                data-question-index="{{ $questionIndex }}"
                            >
                                <div class="element-question-kicker">Питання {{ $questionIndex + 1 }}</div>
                                <h2 class="element-question-title">{{ $question->question }}</h2>

                                <div class="element-answer-grid">
                                    @foreach ($answers as $answerIndex => $answer)
                                        @php
                                            $answerColors = ['#f97316', '#38bdf8', '#0ea5e9', '#22c55e'];
                                            $imageId = $answer->img ?: ((($answer->id - 1) % 30) + 1);
                                        @endphp

                                        <label class="element-answer" style="--answer-color: {{ $answerColors[$answerIndex % count($answerColors)] }}">
                                            <input
                                                type="radio"
                                                name="answers[{{ $question->id }}]"
                                                value="{{ $answer->id }}"
                                                required
                                            >
                                            <span class="element-answer-card">
                                                <img src="{{ asset('assets/images/answer/' . $imageId . '.png') }}" alt="{{ $answer->answer }}">
                                                <span class="element-answer-check">✓</span>
                                                <span class="element-answer-copy">
                                                    <span class="element-answer-mark">{{ chr(65 + $answerIndex) }}</span>
                                                    <span class="element-answer-text">{{ $answer->answer }}</span>
                                                </span>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </section>
                        @endforeach
                    </div>

                    <section class="element-reveal" data-reveal aria-live="polite">
                        <div class="element-reveal-ring">
                            <div class="element-reveal-core">Стихія проявляється...</div>
                        </div>
                    </section>

                    <div class="element-sort-actions" data-actions>
                        <button class="element-sort-button secondary" type="button" data-prev-button disabled>Назад</button>
                        <button class="element-sort-button primary" type="button" data-next-button disabled>Далі</button>
                        <button class="element-sort-button finish" type="submit" data-finish-button disabled>Відкрити команду</button>
                    </div>
                </form>
            </main>
        </div>
    </div>

    <script>
        (() => {
            const root = document.querySelector('[data-element-sort]');

            if (!root) {
                return;
            }

            const stage = root.querySelector('.element-sort-stage');
            const questions = Array.from(root.querySelectorAll('[data-question]'));
            const progressBar = root.querySelector('[data-progress-bar]');
            const progressText = root.querySelector('[data-progress-text]');
            const previousButton = root.querySelector('[data-prev-button]');
            const nextButton = root.querySelector('[data-next-button]');
            const finishButton = root.querySelector('[data-finish-button]');
            const userSelect = document.getElementById('user_id');
            const userWarning = root.querySelector('[data-user-warning]');
            const form = document.getElementById('element_sort_form');
            const actions = root.querySelector('[data-actions]');
            const reveal = root.querySelector('[data-reveal]');
            const questionList = root.querySelector('[data-question-list]');
            let currentQuestion = 0;

            const hasAnswer = (index) => {
                return Boolean(questions[index]?.querySelector('input[type="radio"]:checked'));
            };

            const hasEveryAnswer = () => {
                return questions.every((question) => Boolean(question.querySelector('input[type="radio"]:checked')));
            };

            const render = () => {
                questions.forEach((question, index) => {
                    question.classList.toggle('is-active', index === currentQuestion);
                    question.classList.remove('needs-answer');
                });

                const progress = questions.length ? ((currentQuestion + 1) / questions.length) * 100 : 0;
                progressBar.style.width = `${progress}%`;
                progressText.textContent = `${currentQuestion + 1} / ${questions.length}`;

                previousButton.disabled = currentQuestion === 0;
                nextButton.disabled = !hasAnswer(currentQuestion);
                nextButton.style.display = currentQuestion === questions.length - 1 ? 'none' : 'inline-flex';

                finishButton.classList.toggle('is-visible', currentQuestion === questions.length - 1);
                finishButton.disabled = currentQuestion !== questions.length - 1 || !hasEveryAnswer() || !userSelect.value;

                if (userSelect.value) {
                    userWarning.hidden = true;
                    stage.classList.remove('needs-user');
                }
            };

            const goToQuestion = (index) => {
                currentQuestion = Math.max(0, Math.min(index, questions.length - 1));
                render();
            };

            questions.forEach((question, index) => {
                question.querySelectorAll('input[type="radio"]').forEach((input) => {
                    input.addEventListener('change', () => {
                        render();

                        if (index < questions.length - 1) {
                            window.setTimeout(() => goToQuestion(index + 1), 210);
                        }
                    });
                });
            });

            userSelect.addEventListener('change', render);
            previousButton.addEventListener('click', () => goToQuestion(currentQuestion - 1));

            nextButton.addEventListener('click', () => {
                if (!hasAnswer(currentQuestion)) {
                    questions[currentQuestion].classList.add('needs-answer');
                    return;
                }

                goToQuestion(currentQuestion + 1);
            });

            form.addEventListener('submit', (event) => {
                if (!userSelect.value || !hasEveryAnswer()) {
                    event.preventDefault();

                    if (!userSelect.value) {
                        userWarning.hidden = false;
                        stage.classList.add('needs-user');
                    }

                    if (!hasAnswer(currentQuestion)) {
                        questions[currentQuestion].classList.add('needs-answer');
                    }

                    render();
                    return;
                }

                if (!form.dataset.revealed) {
                    event.preventDefault();
                    form.dataset.revealed = 'true';
                    questionList.hidden = true;
                    actions.hidden = true;
                    reveal.classList.add('is-visible');

                    window.setTimeout(() => form.submit(), 1200);
                }
            });

            render();
        })();
    </script>
@endsection
