@extends('layouts.auth')

@section('title', 'Вхід - CHERRY CAMP')

@section('content')
    <div class="mx-auto w-full" style="max-width: 420px;">
        <div class="mb-8 flex items-center gap-3">
            <img class="h-11 w-11 rounded-full object-cover" src="{{ asset('assets/images/logo.png') }}" alt="CHERRY CAMP">
            <div>
                <div class="text-xl font-extrabold text-black dark:text-white">CHERRY CAMP</div>
                <div class="text-sm text-white-dark">Адмін-панель табору</div>
            </div>
        </div>

        <div class="rounded-md border border-white-light bg-white p-6 shadow-sm dark:border-[#1b2e4b] dark:bg-[#0e1726]">
            <div class="mb-5">
                <h1 class="text-xl font-bold text-black dark:text-white">Вхід за PIN-кодом</h1>
                <p class="mt-1 text-sm text-white-dark">Введіть адміністративний PIN, щоб перейти до керування заїздами, іграми та голосуваннями.</p>
            </div>

            @if(session('success'))
                <div class="mb-4 rounded border border-success bg-success-light p-3 text-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded border border-danger bg-danger-light p-3 text-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('admin.login.submit') }}" method="POST">
                @csrf
                <label for="pin" class="mb-2 block font-semibold">PIN-код</label>
                <input
                    type="password"
                    name="pin"
                    id="pin"
                    class="form-input"
                    inputmode="numeric"
                    autocomplete="current-password"
                    autofocus
                    required
                >

                <button type="submit" class="btn btn-primary mt-5 w-full justify-center">Увійти</button>
            </form>
        </div>

        <div class="mt-5 rounded border border-white-light bg-white px-4 py-3 text-xs text-white-dark dark:border-[#1b2e4b] dark:bg-[#0e1726]">
            Доступ призначений лише для адміністраторів CHERRY CAMP.
        </div>
    </div>
@endsection
