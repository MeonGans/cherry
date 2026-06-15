@extends('layouts.app2')

@section('content')
    <style>
        @media (min-width: 1024px) {
            body:has(.sorting2-shell) .main-container .main-content {
                margin-left: 0 !important;
                margin-right: 0 !important;
            }
        }

        .sorting2-shell {
            width: min(1280px, calc(100vw - 48px));
            min-height: calc(100vh - 48px);
            margin: 0 auto;
            padding: 24px;
            color: #172033;
            background:
                linear-gradient(135deg, rgba(255, 247, 237, 0.96), rgba(224, 242, 254, 0.92) 48%, rgba(240, 253, 244, 0.94)),
                url("{{ asset('assets/images/knowledge/pattern.png') }}");
            background-size: cover, 420px;
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 8px;
        }

        .sorting2-layout {
            display: grid;
            grid-template-columns: minmax(240px, 340px) minmax(0, 1fr);
            gap: 20px;
            max-width: 100%;
            margin: 0 auto;
        }

        .sorting2-side,
        .sorting2-stage {
            border: 1px solid rgba(15, 23, 42, 0.1);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 18px 48px rgba(15, 23, 42, 0.12);
            backdrop-filter: blur(10px);
        }

        .sorting2-side {
            padding: 22px;
            align-self: start;
        }

        .sorting2-stage {
            min-height: 620px;
            padding: 24px;
            overflow: hidden;
        }

        .sorting2-title {
            margin: 0;
            font-size: 2.25rem;
            line-height: 1.05;
            font-weight: 800;
            color: #0f172a;
        }

        .sorting2-subtitle {
            margin-top: 12px;
            color: #475569;
            font-size: 1rem;
            line-height: 1.6;
        }

        .sorting2-emblems {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 8px;
            margin-top: 24px;
        }

        .sorting2-emblems img {
            width: 100%;
            aspect-ratio: 1;
            object-fit: contain;
            border-radius: 8px;
            background: rgba(248, 250, 252, 0.78);
            padding: 8px;
        }

        .sorting2-field {
            margin-top: 24px;
        }

        .sorting2-label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.9rem;
            font-weight: 800;
            color: #334155;
        }

        .sorting2-select {
            width: 100%;
            min-height: 46px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #ffffff;
            padding: 0 14px;
            color: #0f172a;
            font-size: 1rem;
            outline: none;
        }

        .sorting2-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.14);
        }

        .sorting2-empty {
            margin-top: 18px;
            border-radius: 8px;
            background: #fff7ed;
            padding: 14px;
            color: #9a3412;
            font-weight: 700;
        }

        .sorting2-progress {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 16px;
            align-items: center;
            margin-bottom: 24px;
        }

        .sorting2-progress-track {
            height: 10px;
            overflow: hidden;
            border-radius: 8px;
            background: #e2e8f0;
        }

        .sorting2-progress-bar {
            width: 25%;
            height: 100%;
            border-radius: 8px;
            background: linear-gradient(90deg, #ef4444, #0ea5e9, #22c55e, #94a3b8);
            transition: width 220ms ease;
        }

        .sorting2-progress-text {
            min-width: 72px;
            text-align: right;
            color: #475569;
            font-weight: 800;
        }

        .sorting2-question {
            display: none;
            animation: sorting2Enter 180ms ease-out;
        }

        .sorting2-question.is-active {
            display: block;
        }

        .sorting2-kicker {
            display: inline-flex;
            align-items: center;
            min-height: 32px;
            border-radius: 8px;
            background: #eff6ff;
            padding: 0 12px;
            color: #1d4ed8;
            font-weight: 800;
        }

        .sorting2-prompt {
            margin: 18px 0 0;
            font-size: 1.85rem;
            line-height: 1.22;
            font-weight: 800;
            color: #111827;
        }

        .sorting2-answers {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            margin-top: 28px;
        }

        .sorting2-answer {
            position: relative;
            display: block;
            min-height: 116px;
            cursor: pointer;
        }

        .sorting2-answer input {
            position: absolute;
            inset: 0;
            opacity: 0;
        }

        .sorting2-answer-body {
            display: grid;
            grid-template-columns: 40px 1fr;
            gap: 14px;
            align-items: center;
            height: 100%;
            min-height: 116px;
            border: 1px solid #dbe3ef;
            border-radius: 8px;
            background: #ffffff;
            padding: 18px;
            color: #1e293b;
            font-size: 1.03rem;
            font-weight: 800;
            line-height: 1.35;
            transition: transform 160ms ease, border-color 160ms ease, box-shadow 160ms ease, background 160ms ease;
        }

        .sorting2-answer:hover .sorting2-answer-body {
            transform: translateY(-2px);
            border-color: #93c5fd;
            box-shadow: 0 16px 30px rgba(15, 23, 42, 0.12);
        }

        .sorting2-answer input:checked + .sorting2-answer-body,
        .sorting2-answer input:focus-visible + .sorting2-answer-body {
            border-color: #2563eb;
            background: linear-gradient(135deg, #eff6ff, #ffffff);
            box-shadow: 0 18px 34px rgba(37, 99, 235, 0.18);
        }

        .sorting2-answer-mark {
            display: inline-grid;
            place-items: center;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: #0f172a;
            color: #ffffff;
            font-weight: 900;
        }

        .sorting2-actions {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-top: 26px;
        }

        .sorting2-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 132px;
            min-height: 46px;
            border: 1px solid transparent;
            border-radius: 8px;
            padding: 0 18px;
            font-weight: 900;
            transition: transform 160ms ease, box-shadow 160ms ease, opacity 160ms ease;
        }

        .sorting2-button:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.12);
        }

        .sorting2-button:disabled {
            cursor: not-allowed;
            opacity: 0.45;
        }

        .sorting2-button.secondary {
            border-color: #cbd5e1;
            background: #ffffff;
            color: #334155;
        }

        .sorting2-button.primary {
            background: #0f172a;
            color: #ffffff;
        }

        .sorting2-button.finish {
            display: none;
            background: linear-gradient(135deg, #0f172a, #2563eb);
            color: #ffffff;
        }

        .sorting2-button.finish.is-visible {
            display: inline-flex;
        }

        .sorting2-question.needs-answer .sorting2-answers {
            animation: sorting2Pulse 220ms ease;
        }

        @keyframes sorting2Enter {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes sorting2Pulse {
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

        @media (max-width: 900px) {
            .sorting2-layout {
                grid-template-columns: 1fr;
            }

            .sorting2-stage {
                min-height: 0;
            }
        }

        @media (max-width: 640px) {
            .sorting2-shell {
                width: calc(100vw - 28px);
                padding: 14px;
            }

            .sorting2-side,
            .sorting2-stage {
                padding: 16px;
            }

            .sorting2-title {
                font-size: 1.75rem;
            }

            .sorting2-prompt {
                font-size: 1.35rem;
            }

            .sorting2-answers {
                grid-template-columns: 1fr;
            }

            .sorting2-actions {
                flex-direction: column-reverse;
            }

            .sorting2-button {
                width: 100%;
            }
        }
    </style>

    <div class="sorting2-shell" data-sorting-two>
        <div class="sorting2-layout">
            <aside class="sorting2-side">
                <h1 class="sorting2-title">Сортування 2.0</h1>
                <p class="sorting2-subtitle">Чотири короткі вибори, один маршрут і фінальний знак команди.</p>

                <div class="sorting2-emblems" aria-hidden="true">
                    <img src="{{ asset('assets/images/elements/1.png') }}" alt="">
                    <img src="{{ asset('assets/images/elements/2.png') }}" alt="">
                    <img src="{{ asset('assets/images/elements/3.png') }}" alt="">
                    <img src="{{ asset('assets/images/elements/4.png') }}" alt="">
                    <img src="{{ asset('assets/images/elements/5.png') }}" alt="">
                </div>

                <div class="sorting2-field">
                    <label class="sorting2-label" for="sorting2_user_id">Учасник</label>
                    <select class="sorting2-select" name="user_id" id="sorting2_user_id" form="sorting2_form" required>
                        <option value="">Оберіть імʼя</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                @if($users->isEmpty())
                    <div class="sorting2-empty">Немає учнів, готових до сортування в активній сесії.</div>
                @endif
            </aside>

            <main class="sorting2-stage">
                <form id="sorting2_form" action="{{ route('sorting2.handle') }}" method="POST">
                    @csrf

                    <div class="sorting2-progress" aria-live="polite">
                        <div class="sorting2-progress-track">
                            <div class="sorting2-progress-bar" data-progress-bar></div>
                        </div>
                        <div class="sorting2-progress-text" data-progress-text>1 / {{ count($questions) }}</div>
                    </div>

                    @foreach($questions as $questionIndex => $question)
                        <section
                            class="sorting2-question {{ $questionIndex === 0 ? 'is-active' : '' }}"
                            data-question
                            data-question-index="{{ $questionIndex }}"
                        >
                            <div class="sorting2-kicker">Питання {{ $questionIndex + 1 }}</div>
                            <h2 class="sorting2-prompt">{{ $question['prompt'] }}</h2>

                            <div class="sorting2-answers">
                                @foreach($question['options'] as $optionIndex => $option)
                                    <label class="sorting2-answer">
                                        <input
                                            type="radio"
                                            name="answers[{{ $questionIndex }}]"
                                            value="{{ $option }}"
                                            required
                                        >
                                        <span class="sorting2-answer-body">
                                            <span class="sorting2-answer-mark">{{ chr(65 + $optionIndex) }}</span>
                                            <span>{{ $option }}</span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </section>
                    @endforeach

                    <div class="sorting2-actions">
                        <button class="sorting2-button secondary" type="button" data-prev-button disabled>Назад</button>
                        <button class="sorting2-button primary" type="button" data-next-button disabled>Далі</button>
                        <button class="sorting2-button finish" type="submit" data-finish-button disabled>Завершити</button>
                    </div>
                </form>
            </main>
        </div>
    </div>

    <script>
        (() => {
            const root = document.querySelector('[data-sorting-two]');

            if (!root) {
                return;
            }

            const questions = Array.from(root.querySelectorAll('[data-question]'));
            const progressBar = root.querySelector('[data-progress-bar]');
            const progressText = root.querySelector('[data-progress-text]');
            const previousButton = root.querySelector('[data-prev-button]');
            const nextButton = root.querySelector('[data-next-button]');
            const finishButton = root.querySelector('[data-finish-button]');
            const userSelect = document.getElementById('sorting2_user_id');
            const form = document.getElementById('sorting2_form');
            let currentQuestion = 0;

            const hasAnswer = (index) => {
                return Boolean(questions[index].querySelector('input[type="radio"]:checked'));
            };

            const hasEveryAnswer = () => {
                return questions.every((question) => Boolean(question.querySelector('input[type="radio"]:checked')));
            };

            const render = () => {
                questions.forEach((question, index) => {
                    question.classList.toggle('is-active', index === currentQuestion);
                    question.classList.remove('needs-answer');
                });

                const progress = ((currentQuestion + 1) / questions.length) * 100;
                progressBar.style.width = `${progress}%`;
                progressText.textContent = `${currentQuestion + 1} / ${questions.length}`;

                previousButton.disabled = currentQuestion === 0;
                nextButton.disabled = !hasAnswer(currentQuestion);
                nextButton.style.display = currentQuestion === questions.length - 1 ? 'none' : 'inline-flex';

                finishButton.classList.toggle('is-visible', currentQuestion === questions.length - 1);
                finishButton.disabled = currentQuestion !== questions.length - 1 || !hasEveryAnswer() || !userSelect.value;
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
                            window.setTimeout(() => goToQuestion(index + 1), 180);
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
                    questions[currentQuestion].classList.add('needs-answer');
                    render();
                }
            });

            render();
        })();
    </script>
@endsection
