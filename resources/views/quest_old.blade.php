@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Введіть пароль</h1>
        <form action="{{ route('quest.handle') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="password">Пароль</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <button type="submit" class="btn btn-primary">Відправити</button>
        </form>
    </div>
@endsection
