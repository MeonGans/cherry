<!DOCTYPE html>
<html lang="uk" dir="ltr">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <title>@yield('title', 'CHERRY CAMP')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}"/>
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/style.css') }}"/>
    <style>
        :root {
            --vote-ink: #151827;
            --vote-muted: #687083;
            --vote-line: #e4e8f0;
            --vote-bg: #f7f8fb;
            --vote-card: #ffffff;
            --vote-primary: #d9234e;
            --vote-primary-dark: #b4173d;
            --vote-success: #138a61;
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            background:
                linear-gradient(135deg, rgba(217, 35, 78, 0.08), transparent 34%),
                linear-gradient(315deg, rgba(39, 110, 241, 0.08), transparent 38%),
                var(--vote-bg);
            color: var(--vote-ink);
            font-family: Nunito, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            font-size: 15px;
            line-height: 1.5;
        }

        .vote-page {
            display: grid;
            min-height: 100vh;
            place-items: center;
            padding: 28px 16px;
        }

        .vote-shell {
            width: min(100%, 760px);
        }

        .vote-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 18px;
            color: var(--vote-primary);
            font-size: 13px;
            font-weight: 900;
            letter-spacing: 0;
            text-transform: uppercase;
        }

        .vote-brand-mark {
            display: grid;
            width: 34px;
            height: 34px;
            place-items: center;
            border-radius: 10px;
            background: var(--vote-primary);
            color: #ffffff;
            box-shadow: 0 12px 24px rgba(217, 35, 78, 0.22);
        }

        .vote-card {
            overflow: hidden;
            border: 1px solid rgba(228, 232, 240, 0.9);
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.94);
            box-shadow: 0 24px 80px rgba(21, 24, 39, 0.12);
        }

        .vote-card-inner {
            padding: clamp(24px, 5vw, 44px);
        }

        .vote-kicker {
            display: inline-flex;
            align-items: center;
            min-height: 30px;
            padding: 6px 12px;
            border-radius: 999px;
            background: rgba(217, 35, 78, 0.1);
            color: var(--vote-primary);
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .vote-title {
            margin: 14px 0 8px;
            color: var(--vote-ink);
            font-size: clamp(28px, 6vw, 44px);
            font-weight: 900;
            line-height: 1.08;
        }

        .vote-copy {
            max-width: 590px;
            margin: 0;
            color: var(--vote-muted);
            font-size: 16px;
        }

        .vote-form {
            margin-top: 26px;
        }

        .vote-field {
            display: grid;
            gap: 8px;
        }

        .vote-label {
            color: var(--vote-ink);
            font-size: 14px;
            font-weight: 800;
        }

        .vote-input {
            width: 100%;
            min-height: 54px;
            border: 1px solid var(--vote-line);
            border-radius: 14px;
            background: #ffffff;
            color: var(--vote-ink);
            font: inherit;
            font-size: 18px;
            font-weight: 800;
            outline: none;
            padding: 0 16px;
            transition: border-color 160ms ease, box-shadow 160ms ease;
        }

        .vote-input:focus {
            border-color: var(--vote-primary);
            box-shadow: 0 0 0 4px rgba(217, 35, 78, 0.13);
        }

        .vote-button {
            display: inline-flex;
            width: 100%;
            min-height: 54px;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 14px;
            background: var(--vote-primary);
            color: #ffffff;
            cursor: pointer;
            font: inherit;
            font-size: 15px;
            font-weight: 900;
            padding: 0 18px;
            text-decoration: none;
            transition: background-color 160ms ease, box-shadow 160ms ease, transform 160ms ease;
        }

        .vote-button:hover:not(:disabled) {
            background: var(--vote-primary-dark);
            box-shadow: 0 16px 30px rgba(217, 35, 78, 0.22);
            transform: translateY(-1px);
        }

        .vote-button:disabled {
            background: #c7ccd8;
            box-shadow: none;
            cursor: not-allowed;
        }

        .vote-error,
        .vote-success {
            margin-top: 20px;
            border-radius: 14px;
            font-weight: 800;
            padding: 14px 16px;
        }

        .vote-error {
            border: 1px solid rgba(217, 35, 78, 0.22);
            background: rgba(217, 35, 78, 0.08);
            color: #b4173d;
        }

        .vote-success {
            border: 1px solid rgba(19, 138, 97, 0.22);
            background: rgba(19, 138, 97, 0.08);
            color: var(--vote-success);
        }

        .vote-actions {
            margin-top: 24px;
        }

        @media (max-width: 520px) {
            .vote-page {
                align-items: stretch;
                padding: 18px 12px;
            }

            .vote-card {
                border-radius: 18px;
            }

            .vote-brand {
                justify-content: flex-start;
                padding-left: 2px;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <main class="vote-page">
        <div class="vote-shell">
            <div class="vote-brand" aria-label="CHERRY CAMP">
                <span class="vote-brand-mark">C</span>
                <span>CHERRY CAMP</span>
            </div>

            @yield('content')
        </div>
    </main>

    @yield('scripts')
</body>
</html>
