@extends('layouts.app2')

@section('content')
    @php
        // Щоб повернути етап розстановки слів, змініть на true.
        $showOrderStage = false;
    @endphp

    <main class="wednesday-quest">
        <div class="quest-frame">
            <section class="quest-card">
                <div class="quest-mark" aria-hidden="true">W</div>
                <p class="quest-kicker">Nevermore code room</p>
                <h1>Введіть пароль</h1>
                <p class="quest-copy">Фінальний шифр складається з дев'яти букв.</p>

                @if($showOrderStage)
                    <section class="quest-order-stage" data-order-stage>
                        <h2>Розташуйте слова у правильному порядку</h2>
                        <div id="word-container" class="quest-words"></div>
                        <button id="check-order-btn" type="button" class="quest-secondary-button">
                            Перевірити порядок
                        </button>
                        <div id="order-status" class="quest-status"></div>
                    </section>
                @endif

                <section id="password-section" class="quest-password {{ $showOrderStage ? 'is-locked' : '' }}">
                    <form action="{{ route('quest.handle') }}" method="POST" id="password-form" autocomplete="off">
                        @csrf
                        <input type="hidden" name="password" id="password-value" value="">

                        <div class="quest-pin-grid" id="password-fields" aria-label="Пароль з 9 букв">
                            @for ($i = 0; $i < 9; $i++)
                                <input
                                    type="text"
                                    name="password_chars[]"
                                    maxlength="1"
                                    inputmode="text"
                                    pattern="[\p{L}]"
                                    required
                                    class="pin-input"
                                    autocomplete="off"
                                    aria-label="Буква {{ $i + 1 }}"
                                >
                            @endfor
                        </div>

                        @if ($errors->any())
                            <div class="quest-alert">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <button type="submit" class="quest-submit">Відкрити сейф</button>
                    </form>
                </section>
            </section>
        </div>
    </main>

    <style>
        .wednesday-quest {
            --ink: #07070a;
            --night: #111018;
            --violet: #4c2a7a;
            --wine: #6b1f3a;
            --silver: #d8d3df;
            --paper: #f1edf4;
            min-height: calc(100vh - 48px);
            margin: -1.5rem;
            display: grid;
            place-items: center;
            padding: clamp(20px, 5vw, 64px);
            color: var(--paper);
            background:
                linear-gradient(115deg, rgba(107, 31, 58, 0.24), transparent 34%),
                linear-gradient(245deg, rgba(76, 42, 122, 0.28), transparent 38%),
                repeating-linear-gradient(90deg, rgba(255, 255, 255, 0.035) 0 1px, transparent 1px 42px),
                linear-gradient(180deg, #15131c, #060609 72%);
            font-family: Georgia, "Times New Roman", serif;
            overflow: hidden;
        }

        .wednesday-quest::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            background:
                linear-gradient(90deg, rgba(0, 0, 0, 0.72), transparent 18%, transparent 82%, rgba(0, 0, 0, 0.72)),
                linear-gradient(180deg, rgba(0, 0, 0, 0.58), transparent 30%, rgba(0, 0, 0, 0.68));
        }

        .quest-frame {
            position: relative;
            z-index: 1;
            width: min(100%, 820px);
            padding: clamp(10px, 2vw, 18px);
            border: 1px solid rgba(216, 211, 223, 0.22);
            background:
                linear-gradient(135deg, rgba(216, 211, 223, 0.12), rgba(216, 211, 223, 0.03)),
                rgba(7, 7, 10, 0.62);
            box-shadow:
                0 28px 90px rgba(0, 0, 0, 0.66),
                inset 0 0 0 1px rgba(255, 255, 255, 0.04);
        }

        .quest-card {
            position: relative;
            padding: clamp(28px, 5vw, 58px);
            border: 1px solid rgba(216, 211, 223, 0.22);
            background:
                linear-gradient(180deg, rgba(17, 16, 24, 0.96), rgba(7, 7, 10, 0.94)),
                var(--night);
            text-align: center;
            overflow: hidden;
        }

        .quest-card::before,
        .quest-card::after {
            content: "";
            position: absolute;
            left: 24px;
            right: 24px;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(216, 211, 223, 0.52), transparent);
        }

        .quest-card::before {
            top: 18px;
        }

        .quest-card::after {
            bottom: 18px;
        }

        .quest-mark {
            width: 54px;
            height: 54px;
            margin: 0 auto 18px;
            display: grid;
            place-items: center;
            border: 1px solid rgba(216, 211, 223, 0.42);
            color: var(--silver);
            background: #08070b;
            font-size: 28px;
            font-weight: 700;
            line-height: 1;
            box-shadow: 0 0 28px rgba(76, 42, 122, 0.42);
        }

        .quest-kicker {
            margin: 0 0 10px;
            color: rgba(216, 211, 223, 0.72);
            font-family: Arial, sans-serif;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.16em;
            text-transform: uppercase;
        }

        .quest-card h1 {
            margin: 0;
            color: #ffffff;
            font-size: clamp(34px, 7vw, 78px);
            line-height: 0.95;
            font-weight: 700;
            text-shadow: 0 0 34px rgba(107, 31, 58, 0.34);
        }

        .quest-copy {
            margin: 16px auto 30px;
            max-width: 420px;
            color: rgba(241, 237, 244, 0.72);
            font-family: Arial, sans-serif;
            font-size: clamp(14px, 2vw, 17px);
            line-height: 1.5;
        }

        .quest-password.is-locked {
            display: none;
        }

        .quest-pin-grid {
            display: grid;
            grid-template-columns: repeat(9, minmax(34px, 1fr));
            gap: clamp(6px, 1.4vw, 12px);
            width: min(100%, 620px);
            margin: 0 auto;
        }

        .pin-input {
            width: 100%;
            aspect-ratio: 0.78;
            min-height: 54px;
            border: 1px solid rgba(216, 211, 223, 0.34);
            border-radius: 2px;
            color: #ffffff;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0.02)),
                #0a0a0f;
            font-family: Georgia, "Times New Roman", serif;
            font-size: clamp(22px, 4vw, 40px);
            font-weight: 700;
            line-height: 1;
            text-align: center;
            text-transform: lowercase;
            box-shadow:
                inset 0 -18px 30px rgba(76, 42, 122, 0.12),
                0 10px 24px rgba(0, 0, 0, 0.28);
            transition: border-color 180ms ease, box-shadow 180ms ease, transform 180ms ease;
        }

        .pin-input:focus {
            outline: none;
            border-color: rgba(216, 211, 223, 0.82);
            box-shadow:
                inset 0 -18px 30px rgba(76, 42, 122, 0.18),
                0 0 0 3px rgba(107, 31, 58, 0.26),
                0 14px 26px rgba(0, 0, 0, 0.34);
            transform: translateY(-2px);
        }

        .quest-alert {
            width: min(100%, 520px);
            margin: 22px auto 0;
            padding: 12px 14px;
            border: 1px solid rgba(185, 48, 81, 0.54);
            color: #ffd8e2;
            background: rgba(107, 31, 58, 0.24);
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.4;
        }

        .quest-submit,
        .quest-secondary-button {
            min-height: 50px;
            margin-top: 28px;
            padding: 0 28px;
            border: 1px solid rgba(216, 211, 223, 0.38);
            border-radius: 2px;
            color: #ffffff;
            background:
                linear-gradient(135deg, rgba(107, 31, 58, 0.95), rgba(76, 42, 122, 0.92)),
                #3c1732;
            cursor: pointer;
            font-family: Arial, sans-serif;
            font-size: 14px;
            font-weight: 900;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            box-shadow: 0 18px 34px rgba(0, 0, 0, 0.4);
        }

        .quest-submit:hover,
        .quest-secondary-button:hover {
            filter: brightness(1.08);
        }

        .quest-order-stage {
            margin: 28px auto;
            padding: 20px;
            border: 1px dashed rgba(216, 211, 223, 0.28);
        }

        .quest-order-stage h2 {
            margin: 0 0 16px;
            color: #ffffff;
            font-size: 22px;
        }

        .quest-words {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            min-height: 58px;
        }

        .quest-words .word-btn {
            cursor: grab;
            user-select: none;
        }

        .quest-status {
            margin-top: 14px;
            font-family: Arial, sans-serif;
        }

        @media (max-width: 700px) {
            .quest-pin-grid {
                grid-template-columns: repeat(9, minmax(28px, 1fr));
            }

            .pin-input {
                min-height: 46px;
            }
        }

        @media (max-width: 480px) {
            .wednesday-quest {
                padding: 14px;
            }

            .quest-card {
                padding: 26px 14px;
            }

            .quest-pin-grid {
                gap: 5px;
            }
        }
    </style>

    @if($showOrderStage)
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    @endif

    <script>
        const showOrderStage = @json($showOrderStage);
        const inputs = Array.from(document.querySelectorAll('.pin-input'));
        const passwordForm = document.getElementById('password-form');
        const passwordValue = document.getElementById('password-value');
        const letterPattern = /^\p{L}$/u;

        function setPasswordValue() {
            passwordValue.value = inputs.map((input) => input.value).join('');
        }

        function fillFromText(text, startIndex = 0) {
            const letters = Array.from(text)
                .map((char) => char.toLocaleLowerCase('uk-UA'))
                .filter((char) => letterPattern.test(char));
            const clearCount = Math.max(1, Math.min(letters.length, inputs.length - startIndex));

            inputs.slice(startIndex, startIndex + clearCount).forEach((input) => {
                input.value = '';
            });

            letters.slice(0, inputs.length - startIndex).forEach((char, offset) => {
                inputs[startIndex + offset].value = char;
            });

            const nextIndex = Math.min(startIndex + letters.length, inputs.length - 1);
            inputs[nextIndex]?.focus();
            setPasswordValue();
        }

        inputs.forEach((input, index) => {
            input.addEventListener('input', () => {
                fillFromText(input.value, index);

                if (input.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });

            input.addEventListener('paste', (event) => {
                event.preventDefault();
                fillFromText(event.clipboardData.getData('text'), index);
            });

            input.addEventListener('keydown', (event) => {
                if (event.key === 'Backspace' && input.value === '' && index > 0) {
                    inputs[index - 1].focus();
                }
            });
        });

        passwordForm.addEventListener('submit', setPasswordValue);
        inputs[0]?.focus();

        if (showOrderStage) {
            const correctOrder = ['рюкзак', 'компас', 'карта', 'бінокль', 'скарб'];
            const shuffledWords = [...correctOrder].sort(() => Math.random() - 0.5);
            const container = document.getElementById('word-container');

            shuffledWords.forEach((word) => {
                const btn = document.createElement('div');
                btn.textContent = word;
                btn.className = 'btn btn-outline-primary word-btn mb-2';
                container.appendChild(btn);
            });

            new Sortable(container, {
                animation: 150,
            });

            document.getElementById('check-order-btn').addEventListener('click', function () {
                const currentOrder = Array.from(container.children).map((btn) => btn.textContent);
                const isCorrect = currentOrder.every((word, index) => word === correctOrder[index]);
                const statusDiv = document.getElementById('order-status');

                if (isCorrect) {
                    statusDiv.innerHTML = '<div class="alert alert-success">Правильний порядок. Пароль відкрито.</div>';
                    document.getElementById('password-section').classList.remove('is-locked');
                    this.disabled = true;
                    inputs[0]?.focus();
                } else {
                    statusDiv.innerHTML = '<div class="alert alert-danger">Неправильний порядок.</div>';
                }
            });
        }
    </script>
@endsection
