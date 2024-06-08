@extends('layouts.app')

@section('content')
    <div class="panel">
        <div class="mb-5 flex items-center justify-between">
            <h5 class="text-lg font-semibold dark:text-white-light">Продукти</h5>
            <!-- contextual -->
        </div>
        <div class="mb-5">
            <a href="{{ route('products.create') }}" class="btn btn-primary">Додати продукт</a>
            <div class="table-responsive">
                <table class="table mt-4">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Назва</th>
                        <th>Кількість</th>
                        <th>Цінність</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($products as $product)
                        <tr>
                            <td>{{ $product->id }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->quantity }}</td>
                            <td>{{ $product->value }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                <p><strong>Загальна кількість продукції:</strong> {{ $totalQuantity }}</p>
                <p><strong>Середня цінність продукції:</strong> {{ number_format($averageValue, 2) }}</p>
            </div>
        </div>
    </div>
@endsection
