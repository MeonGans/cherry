@extends('layouts.app')

@section('content')
    <div class="panel">
        <div class="mb-5 flex items-center justify-between">
            <h5 class="text-lg font-semibold dark:text-white-light">Створити голосування</h5>
            <!-- contextual -->
        </div>
        <div class="mb-5">
    <form action="{{ route('votes.store') }}" method="POST">
        @csrf
        <div>
            <label for="name">Назва голосування:</label>
            <input type="text" id="name" name="name" class="form-input" required>
        </div>
        <button type="submit" class="btn btn-primary mt-6">Створити</button>
    </form>
        </div>
    </div>
@endsection
