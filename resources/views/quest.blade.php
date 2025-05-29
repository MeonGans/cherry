@extends('layouts.app')

@section('content')
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

    <style>
        .pin-input {
            width: 50px;
            height: 60px;
            font-size: 28px;
            border: 2px solid #ced4da;
            border-radius: 8px;
            transition: border-color 0.3s;
        }

        .pin-input:focus {
            border-color: #007bff;
            outline: none;
        }
    </style>

    <script>
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

        // Об'єднуємо значення в одне поле перед відправкою
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
