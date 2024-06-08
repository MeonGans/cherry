@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Product Details</h1>
        <p><strong>Name:</strong> {{ $product->name }}</p>
        <p><strong>Quantity:</strong> {{ $product->quantity }}</p>
        <p><strong>Value:</strong> {{ $product->value }}</p>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">Back to Products</a>
    </div>
@endsection
