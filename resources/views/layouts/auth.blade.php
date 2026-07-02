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
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/style.css') }}"/>
</head>
<body class="min-h-screen bg-[#f7f8fb] font-nunito text-sm font-normal text-black antialiased dark:bg-[#060818] dark:text-white-dark">
    <main class="grid min-h-screen place-items-center px-4 py-10">
        @yield('content')
    </main>
</body>
</html>
