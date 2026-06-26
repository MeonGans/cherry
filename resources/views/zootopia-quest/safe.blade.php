@extends('layouts.app2')

@section('content')
    @php
        $correctOrder = ['рюкзак', 'компас', 'карта', 'бінокль', 'скарб'];
    @endphp

    <main class="zootopia-safe">
        <section class="zootopia-safe-card">
            <div class="zootopia-safe-mark" aria-hidden="true">ZPD</div>
            <p class="zootopia-safe-kicker">Evidence vault</p>
            <h1>Введіть пароль скрині</h1>
            <p class="zootopia-safe-copy">Фінальний шифр складається з дев'яти букв.</p>

            <section class="zootopia-order-stage" data-order-stage>
                <p class="zootopia-step-label">Завдання 1</p>
                <h2>Розташуйте слова у правильному порядку</h2>
                <div id="word-container" class="zootopia-words"></div>
                <button id="check-order-btn" type="button" class="zootopia-secondary">
                    Перевірити порядок
                </button>
                <div id="order-status" class="zootopia-status-message" aria-live="polite"></div>
            </section>

            <section id="password-section" class="zootopia-password is-locked">
                <form action="{{ route('zootopia.safe.handle') }}" method="POST" id="password-form" autocomplete="off">
                    @csrf
                    <input type="hidden" name="password" id="password-value" value="">

                    <div class="zootopia-pin-grid" id="password-fields" aria-label="Пароль з 9 букв">
                        @for ($i = 0; $i < 9; $i++)
                            <input
                                type="text"
                                name="password_chars[]"
                                maxlength="1"
                                inputmode="text"
                                pattern="[\p{L}]"
                                required
                                class="zootopia-pin-input"
                                autocomplete="off"
                                aria-label="Буква {{ $i + 1 }}"
                            >
                        @endfor
                    </div>

                    @if ($errors->any())
                        <div class="zootopia-safe-alert">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <button type="submit" class="zootopia-submit">Відкрити скриню</button>
                </form>
            </section>
        </section>
    </main>

    <style>
        @media (min-width: 1024px) {
            body:has(.zootopia-safe) .main-container .main-content {
                margin-left: 0 !important;
                margin-right: 0 !important;
            }
        }

        .zootopia-safe {
            --safe-navy: #071740;
            --safe-blue: #0b4f9b;
            --safe-cyan: #18c0ca;
            --safe-orange: #ffb545;
            --safe-pink: #ee50c9;
            --safe-paper: #eefaff;
            min-height: calc(100vh - 48px);
            margin: -1.5rem;
            display: grid;
            place-items: center;
            padding: clamp(18px, 4vw, 64px);
            color: var(--safe-paper);
            background:
                linear-gradient(180deg, rgba(7, 23, 64, 0.62), rgba(7, 23, 64, 0.96)),
                url("{{ asset('assets/images/zootopia-police-server.webp') }}") center / cover no-repeat;
            font-family: Nunito, Arial, sans-serif;
            overflow-x: hidden;
        }

        .zootopia-safe-card {
            position: relative;
            width: min(100%, 900px);
            border: 1px solid rgba(157, 226, 255, 0.28);
            border-radius: 30px;
            background:
                linear-gradient(135deg, rgba(24, 192, 202, 0.22), rgba(93, 69, 176, 0.28)),
                rgba(8, 21, 58, 0.92);
            box-shadow:
                0 34px 90px rgba(4, 12, 35, 0.62),
                inset 0 0 0 1px rgba(255, 255, 255, 0.05);
            padding: clamp(22px, 5vw, 54px);
            text-align: center;
            overflow: hidden;
        }

        .zootopia-safe-card::before {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background:
                radial-gradient(circle at 12% 12%, rgba(255, 181, 69, 0.18), transparent 24%),
                radial-gradient(circle at 82% 18%, rgba(238, 80, 201, 0.16), transparent 25%);
        }

        .zootopia-safe-card > * {
            position: relative;
            z-index: 1;
        }

        .zootopia-safe-mark {
            width: 66px;
            height: 66px;
            margin: 0 auto 18px;
            display: grid;
            place-items: center;
            border-radius: 18px;
            color: #19335c;
            background: linear-gradient(135deg, #ffd25d, #ff9f39);
            font-size: 18px;
            font-weight: 1000;
            box-shadow: 0 16px 28px rgba(255, 181, 69, 0.2);
        }

        .zootopia-safe-kicker,
        .zootopia-step-label {
            margin: 0 0 10px;
            color: rgba(238, 250, 255, 0.72);
            font-size: 12px;
            font-weight: 1000;
            letter-spacing: 0.14em;
            text-transform: uppercase;
        }

        .zootopia-safe-card h1 {
            margin: 0;
            color: #ffffff;
            font-size: clamp(34px, 7vw, 76px);
            font-weight: 1000;
            line-height: 0.98;
            text-shadow: 0 10px 28px rgba(0, 0, 0, 0.34);
        }

        .zootopia-safe-copy {
            max-width: 430px;
            margin: 16px auto 0;
            color: rgba(238, 250, 255, 0.76);
            font-size: clamp(15px, 2vw, 18px);
            font-weight: 800;
            line-height: 1.5;
        }

        .zootopia-order-stage,
        .zootopia-password {
            margin-top: 28px;
            border: 1px solid rgba(157, 226, 255, 0.26);
            border-radius: 24px;
            background:
                linear-gradient(135deg, rgba(255, 255, 255, 0.12), rgba(255, 255, 255, 0.04)),
                rgba(7, 23, 64, 0.76);
            padding: clamp(18px, 3vw, 28px);
        }

        .zootopia-order-stage h2 {
            margin: 0 0 18px;
            color: #ffffff;
            font-size: clamp(22px, 3vw, 34px);
            font-weight: 1000;
            line-height: 1.16;
        }

        .zootopia-words {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            min-height: 58px;
        }

        .zootopia-word {
            min-height: 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255, 181, 69, 0.52);
            border-radius: 16px;
            background: rgba(255, 181, 69, 0.12);
            color: #ffffff;
            cursor: grab;
            font-size: 17px;
            font-weight: 1000;
            padding: 0 16px;
            user-select: none;
            touch-action: none;
        }

        .zootopia-word:active {
            cursor: grabbing;
        }

        .zootopia-secondary,
        .zootopia-submit {
            min-height: 54px;
            margin-top: 22px;
            border: 0;
            border-radius: 18px;
            color: #11162b;
            background: linear-gradient(135deg, #ffc044, #ff8d3d 42%, var(--safe-pink) 74%, #8b58ff);
            cursor: pointer;
            font-size: 15px;
            font-weight: 1000;
            letter-spacing: 0.06em;
            padding: 0 28px;
            text-transform: uppercase;
            box-shadow: 0 16px 28px rgba(3, 12, 37, 0.36);
        }

        .zootopia-secondary:disabled {
            cursor: default;
            opacity: 0.76;
        }

        .zootopia-status-message {
            min-height: 28px;
            margin-top: 16px;
            color: rgba(238, 250, 255, 0.82);
            font-size: 16px;
            font-weight: 900;
        }

        .zootopia-status-message.is-success {
            color: #8ef8b4;
        }

        .zootopia-status-message.is-error {
            color: #ffd2c7;
        }

        .zootopia-password.is-locked {
            display: none;
        }

        .zootopia-pin-grid {
            display: grid;
            grid-template-columns: repeat(9, minmax(32px, 1fr));
            gap: clamp(6px, 1.4vw, 12px);
            width: min(100%, 640px);
            margin: 0 auto;
        }

        .zootopia-pin-input {
            width: 100%;
            aspect-ratio: 0.8;
            min-height: 52px;
            border: 1px solid rgba(157, 226, 255, 0.34);
            border-radius: 14px;
            color: #ffffff;
            background: rgba(255, 255, 255, 0.08);
            font-size: clamp(22px, 4vw, 40px);
            font-weight: 1000;
            line-height: 1;
            text-align: center;
            text-transform: lowercase;
            outline: none;
            transition: border-color 160ms ease, box-shadow 160ms ease, transform 160ms ease;
        }

        .zootopia-pin-input:focus {
            border-color: rgba(255, 181, 69, 0.86);
            box-shadow: 0 0 0 4px rgba(255, 181, 69, 0.15);
            transform: translateY(-1px);
        }

        .zootopia-safe-alert {
            width: min(100%, 520px);
            margin: 20px auto 0;
            border: 1px solid rgba(255, 151, 110, 0.66);
            border-radius: 18px;
            background: rgba(119, 35, 55, 0.38);
            color: #ffe2da;
            font-size: 15px;
            font-weight: 900;
            padding: 12px 14px;
        }

        @media (max-width: 680px) {
            .zootopia-safe {
                padding: 14px;
            }

            .zootopia-safe-card {
                border-radius: 24px;
                padding: 22px 14px;
            }

            .zootopia-pin-grid {
                gap: 5px;
            }

            .zootopia-pin-input {
                min-height: 42px;
                border-radius: 10px;
            }
        }
    </style>

    <script src="{{ asset('assets/js/Sortable.min.js') }}"></script>
    <script>
        const correctOrder = @json($correctOrder);
        const shuffledWords = [...correctOrder].sort(() => Math.random() - 0.5);
        const wordContainer = document.getElementById('word-container');
        const orderStatus = document.getElementById('order-status');
        const checkOrderButton = document.getElementById('check-order-btn');
        const passwordSection = document.getElementById('password-section');
        const inputs = Array.from(document.querySelectorAll('.zootopia-pin-input'));
        const passwordForm = document.getElementById('password-form');
        const passwordValue = document.getElementById('password-value');
        const letterPattern = /^\p{L}$/u;

        shuffledWords.forEach((word) => {
            const item = document.createElement('div');
            item.textContent = word;
            item.className = 'zootopia-word';
            wordContainer.appendChild(item);
        });

        new Sortable(wordContainer, {
            animation: 150,
        });

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

        checkOrderButton.addEventListener('click', function () {
            const currentOrder = Array.from(wordContainer.children).map((item) => item.textContent);
            const isCorrect = currentOrder.every((word, index) => word === correctOrder[index]);

            orderStatus.classList.remove('is-success', 'is-error');

            if (isCorrect) {
                orderStatus.textContent = 'Правильний порядок. Пароль відкрито.';
                orderStatus.classList.add('is-success');
                passwordSection.classList.remove('is-locked');
                this.disabled = true;
                inputs[0]?.focus();
            } else {
                orderStatus.textContent = 'Неправильний порядок.';
                orderStatus.classList.add('is-error');
            }
        });
    </script>
@endsection
