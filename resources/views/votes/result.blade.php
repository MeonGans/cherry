@extends('layouts.users.app')

@section('content')
    <div class="panel">
        <div class="mb-5 flex items-center justify-between">
            <h5 class="text-lg font-semibold dark:text-white-light">Результати для {{ $vote->name }}</h5>
            <!-- contextual -->
        </div>
        <div class="mb-5">
            @if($teams->isEmpty())
                <p>Ще немає голосів.</p>
            @else
                <div class="container">
                    @foreach($teams as $teamName => $data)
                        @php
                            $height = $maxVotes > 0 ? ($data['count'] / $maxVotes) * 100 : 0;
                        @endphp
                        <div class="barcontainer">
                            <div class="bar" style="background-color: {{ $data['color'] }}; height: {{ $height }}%;" data-height="{{ $height }}"></div>
                            <div class="label" style="animation-delay: 5s; opacity: 0;">{{ $data['count'] }}</div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .container {
            width: 500px;
            height: 400px;
            overflow: hidden;
            position: relative;
            margin: 50px auto;
            display: flex;
            justify-content: space-around;
        }
        .barcontainer {
            background-color: #ffffff;
            position: relative;
            transform: translateY(-50%);
            top: 50%;
            width: 50px;
            height: 320px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .bar {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 0;
            box-sizing: border-box;
            animation: grow 5s ease-out forwards;
            transform-origin: bottom;
        }
        .label {
            margin-top: 10px;
            text-align: center;
            color: #000000;
            animation: fadein 0.5s forwards;
        }
        @keyframes grow {
            from {
                transform: scaleY(0);
            }
        }
        @keyframes fadein {
            to {
                opacity: 1;
            }
        }
    </style>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.bar').forEach(function(bar) {
                var height = bar.getAttribute('data-height');
                bar.style.height = height + '%';
            });
        });
    </script>
@endsection
