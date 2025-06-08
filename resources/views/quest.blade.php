@extends('layouts.app2')

@section('content')
    <div class="container mb-5">
        <h2 class="mb-3">Розташуйте слова у правильному порядку</h2>
        <div id="word-container" class="d-flex flex-wrap gap-2 mb-3 border p-3 rounded">
            <!-- Сюди вставимо слова -->
        </div>
        <button id="check-order-btn" class="btn btn-success">Перевірити порядок</button>
        <div id="order-status" class="mt-3"></div>
    </div>

    <div id="password-section" style="display: none;">
        <div class="container">
            <h1 class="mb-4">Введіть пароль</h1>
            <form action="{{ route('quest.handle') }}" method="POST" id="password-form">
                @csrf

                <div class="d-flex gap-2 justify-content-center mb-4" id="password-fields">
                    @for ($i = 0; $i < 8; $i++)
                        <input type="text" name="password[]" maxlength="1" pattern="[A-Za-z0-9]" required
                               class="pin-input text-center" autocomplete="off">
                    @endfor
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-primary px-4">Відправити</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        #password-fields {
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: nowrap;
            overflow-x: auto;
        }

        .pin-input {
            width: 50px;
            height: 60px;
            font-size: 28px;
            border: 2px solid #ced4da;
            border-radius: 8px;
            text-align: center;
            flex-shrink: 0;
        }

        .pin-input:focus {
            border-color: #007bff;
            outline: none;
        }

        @media (max-width: 576px) {
            .pin-input {
                width: 40px;
                height: 50px;
                font-size: 22px;
            }
        }

        #password-fields::-webkit-scrollbar {
            display: none;
        }

        #password-fields {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Стиль для слів */
        #word-container .word-btn {
            cursor: grab;
            user-select: none;
        }

        #word-container .word-btn:active {
            cursor: grabbing;
        }
    </style>

    <!-- Підключаємо SortableJS -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <script>
        // Початковий правильний порядок слів
        const correctOrder = ['рюкзак', 'компас', 'карта', 'бінокль', 'скарб'];  // заміни на свої слова

        // Створюємо випадковий порядок
        const shuffledWords = [...correctOrder].sort(() => Math.random() - 0.5);
        const container = document.getElementById('word-container');

        // Вставляємо кнопки
        shuffledWords.forEach(word => {
            const btn = document.createElement('div');
            btn.textContent = word;
            btn.className = 'btn btn-outline-primary word-btn mb-2';
            container.appendChild(btn);
        });

        // Ініціалізуємо Sortable
        new Sortable(container, {
            animation: 150,
        });

        // Перевірка порядку
        document.getElementById('check-order-btn').addEventListener('click', function () {
            const currentOrder = Array.from(container.children).map(btn => btn.textContent);
            const isCorrect = currentOrder.every((word, index) => word === correctOrder[index]);

            const statusDiv = document.getElementById('order-status');
            if (isCorrect) {
                statusDiv.innerHTML = '<div class="alert alert-success">Правильний порядок! Можете ввести пароль.</div>';
                document.getElementById('password-section').style.display = 'block';
                this.disabled = true;
            } else {
                statusDiv.innerHTML = '<div class="alert alert-danger">Неправильний порядок, спробуйте ще раз.</div>';
            }
        });

        // Форма паролю (збираємо в одне поле)
        const inputs = document.querySelectorAll('.pin-input');
        inputs.forEach((input, index) => {
            input.addEventListener('input', () => {
                if (input.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && input.value === '' && index > 0) {
                    inputs[index - 1].focus();
                }
            });
        });

        document.getElementById('password-form').addEventListener('submit', function (e) {
            const joined = Array.from(inputs).map(i => i.value).join('');
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'password';
            hidden.value = joined;
            this.appendChild(hidden);
        });
    </script>
@endsection
