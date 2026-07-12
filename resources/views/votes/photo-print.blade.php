<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Друк фото - {{ $votePhoto->vote?->name ?? 'CHERRY CAMP' }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f3f5f9;
            color: #151827;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .print-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 16px 20px;
            background: #ffffff;
            border-bottom: 1px solid #e4e8f0;
        }

        .print-title {
            min-width: 0;
        }

        .print-title h1,
        .print-title p {
            margin: 0;
        }

        .print-title h1 {
            font-size: 18px;
        }

        .print-title p {
            color: #687083;
            font-size: 14px;
            font-weight: 700;
        }

        .print-button {
            min-height: 42px;
            border: 0;
            border-radius: 8px;
            background: #d9234e;
            color: #ffffff;
            cursor: pointer;
            font: inherit;
            font-weight: 900;
            padding: 0 18px;
        }

        .print-stage {
            display: grid;
            min-height: calc(100vh - 75px);
            place-items: center;
            padding: 24px;
        }

        .print-stage img {
            display: block;
            max-width: 100%;
            max-height: calc(100vh - 130px);
            object-fit: contain;
            background: #ffffff;
            box-shadow: 0 20px 60px rgba(21, 24, 39, .14);
        }

        @media print {
            @page {
                margin: 0;
            }

            body {
                background: #ffffff;
            }

            .print-toolbar {
                display: none;
            }

            .print-stage {
                min-height: 100vh;
                padding: 0;
            }

            .print-stage img {
                width: 100vw;
                height: 100vh;
                max-width: none;
                max-height: none;
                object-fit: contain;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <header class="print-toolbar">
        <div class="print-title">
            <h1>{{ $votePhoto->vote?->name ?? 'Фото' }}</h1>
            <p>{{ $votePhoto->user?->name ?? 'Автор не вказаний' }}</p>
        </div>
        <button type="button" class="print-button" onclick="window.print()">Друк</button>
    </header>

    <main class="print-stage">
        <img src="{{ asset($votePhoto->print_image_path) }}" alt="Фото для друку">
    </main>
</body>
</html>
