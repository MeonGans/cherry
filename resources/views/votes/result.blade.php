@extends('layouts.users.app')

@section('content')
{{--    <div class="panel">--}}
{{--        <div class="mb-5 flex items-center justify-between">--}}
{{--            <h5 class="text-lg font-semibold dark:text-white-light">--}}
{{--                Результати для {{ $vote->name }}--}}
{{--            </h5>--}}
{{--        </div>--}}

        <div class="mt-8 mb-5">
            @if($teams->isEmpty())
                <p>Ще немає голосів.</p>
            @else

                <!-- ГРАФІК -->
                <div id="results" class="results-container">
                    @foreach ($teams as $teamName => $data)
                        @php
                            $height = $maxVotes ? $data['count'] / $maxVotes * 100 : 0;
                            $delay = $loop->index * 2;
                        @endphp

                        <div class="bar-wrapper">
                            <div class="bar-space">
                                <div  class="bar"
                                      style="
                                      --bar-color: {{ $data['color'] }};
                                      transition-delay: {{ $delay }}s;
                                  "
                                      data-height="{{ $height }}">
                                </div>

                                <span class="value"
                                      style="transition-delay: {{ $delay + 0.2 }}s">
                                  {{ $data['count'] }}
                            </span>
                            </div>

                            <span class="team">{{ $teamName }}</span>
                        </div>
                    @endforeach
                </div>
                <button id="revealBtn" class="btn btn-primary mx-auto mt-6 block">
                    Показати результати
                </button>
            @endif
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .results-container {
            width: 100%;
            max-width: 620px;
            height: 800px;
            margin: 0 auto;
            display: flex;
            align-items: flex-end;
            justify-content: space-around;
            gap: 40px;
        }

        .bar-wrapper {
            flex: 0 0 120px;
            max-width: 150px;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .bar-space {
            position: relative;
            flex: 1;
            width: 100%;
            display: flex;
            align-items: flex-end;
            justify-content: center;
        }

        .bar {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 0;
            background: linear-gradient(135deg, var(--bar-color), rgba(0, 0, 0, 0.15));
            border-radius: 6px 6px 0 0;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            transition: height 4s cubic-bezier(.22, .68, 0, 1.15);
        }

        .value {
            position: absolute;
            bottom: 0;
            width: 80%;
            text-align: center;
            margin-bottom: 8px;
            font-weight: 700;
            font-size: 1rem;
            color: white;
            background-color: rgba(0, 0, 0, 0.25);
            border-radius: 12px;
            padding: 4px 6px;
            opacity: 0;
            transition: opacity 0.6s ease, background-color 0.6s ease;
        }

        .results-container.revealed .value {
            opacity: 1;
            background-color: var(--bar-color);
            color: white;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3);
        }

        .team {
            margin-top: 1rem;
            font-size: 1.4rem;
            font-weight: 700;
            text-align: center;
            text-transform: uppercase;
            color: #333;
            letter-spacing: 1px;
            background: linear-gradient(to right, #00000011, #00000005);
            padding: 4px 6px;
            border-radius: 6px;
            max-width: 100%;
            word-break: break-word;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
    </style>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById('revealBtn');
            const wrap = document.getElementById('results');
            const bars = wrap.querySelectorAll('.bar');

            btn.addEventListener('click', () => {
                bars.forEach(bar => {
                    const h = bar.dataset.height;
                    bar.style.height = h + '%';
                    bar.parentElement.style.setProperty('--bar-height', h + '%');
                });

                wrap.classList.add('revealed');
                btn.remove(); // ховаємо кнопку повністю
            });
        });
    </script>
@endsection
