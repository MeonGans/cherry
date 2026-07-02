@extends('layouts.app')

@section('content')
    <div class="panel">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h5 class="text-lg font-semibold dark:text-white-light">Товари</h5>
                <p class="text-sm text-white-dark">Керуйте призами, які потрапляють у Колесо фортуни.</p>
            </div>
            <a href="{{ route('products.create') }}" class="btn btn-primary">Додати товар</a>
        </div>

        @if(session('success'))
            <div class="mb-5 rounded border border-success bg-success-light p-3 text-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-5 rounded border border-danger bg-danger-light p-3 text-danger">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @foreach($products as $product)
            <form id="quick-update-product-{{ $product->id }}" action="{{ route('products.quick-update', $product) }}" method="POST" class="hidden">
                @csrf
                @method('PATCH')
            </form>
        @endforeach

        <div class="mb-5">
            <input
                type="search"
                class="form-input max-w-md"
                placeholder="Пошук товару"
                data-table-search="#products-table"
            >
        </div>

        <div class="table-responsive">
            <table id="products-table" class="table-hover table">
                <thead>
                <tr>
                    <th class="w-20">#</th>
                    <th>Товар</th>
                    <th class="w-40">Кількість</th>
                    <th class="w-40">Цінність</th>
                    <th class="w-64 text-right">Дії</th>
                </tr>
                </thead>
                <tbody>
                @forelse($products as $product)
                    <tr data-empty-row>
                        <td>{{ $product->id }}</td>
                        <td>
                            <div class="flex items-center gap-3">
                                <img
                                    src="{{ $product->image_url }}"
                                    alt="{{ $product->name }}"
                                    class="h-12 w-12 rounded object-cover"
                                >
                                <div>
                                    <div class="font-semibold text-black dark:text-white">{{ $product->name }}</div>
                                    <div class="text-xs text-white-dark">ID: {{ $product->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <input
                                type="number"
                                name="quantity"
                                min="0"
                                step="1"
                                value="{{ old("quantity_{$product->id}", $product->quantity) }}"
                                form="quick-update-product-{{ $product->id }}"
                                class="form-input w-28"
                                required
                            >
                        </td>
                        <td>
                            <input
                                type="number"
                                name="value"
                                min="0"
                                max="999999.99"
                                step="0.01"
                                value="{{ old("value_{$product->id}", $product->value) }}"
                                form="quick-update-product-{{ $product->id }}"
                                class="form-input w-28"
                                required
                            >
                        </td>
                        <td>
                            <div class="flex flex-wrap justify-end gap-2">
                                <button type="submit" form="quick-update-product-{{ $product->id }}" class="btn btn-primary btn-sm">
                                    Зберегти
                                </button>
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-primary btn-sm">
                                    Редагувати
                                </a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Видалити цей товар?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">Видалити</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-white-dark">Товарів ще немає.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5 grid gap-4 sm:grid-cols-2">
            <div class="rounded border border-white-light p-4 dark:border-[#1b2e4b]">
                <div class="text-sm text-white-dark">Загальна кількість продукції</div>
                <div class="mt-1 text-xl font-semibold">{{ $totalQuantity }}</div>
            </div>
            <div class="rounded border border-white-light p-4 dark:border-[#1b2e4b]">
                <div class="text-sm text-white-dark">Середня цінність продукції</div>
                <div class="mt-1 text-xl font-semibold">{{ number_format($averageValue, 2) }}</div>
            </div>
        </div>
    </div>
@endsection
