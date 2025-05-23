@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Авторизація. Введіть пін-код</h1>
        <form action="{{ route('admin.login.submit') }}" method="POST">
            @csrf
            <div class="form-group mp-2">
                <input type="password" name="pin" id="pin" class="form-control" required>
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
            <button type="submit" class="btn btn-primary mt-2">Увійти</button>
        </form>
    </div>
@endsection
