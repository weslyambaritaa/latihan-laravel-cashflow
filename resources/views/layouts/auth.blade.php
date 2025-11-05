<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Cashflow App')</title> {{-- Judul dinamis --}}

    {{-- Bootstrap CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap-5.3.8-dist/css/bootstrap.min.css') }}">
    
    {{-- TAMBAHAN: Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @livewireStyles

    {{-- Style kustom untuk background --}}
    <style>
        body {
            /* Fallback */
            background-color: #f0f2f5; 
            
            /* Gradient background */
            background-image: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            
            /* Agar konten di tengah */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding-top: 40px;
            padding-bottom: 40px;
        }
    </style>
</head>

<body class="bg-light">

    {{-- Konten akan dimuat di sini --}}
    <main class="container">
        @yield('content')
    </main>

    {{-- Bootstrap JS --}}
    <script src="{{ asset('assets/vendor/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js') }}"></script>
    @livewireScripts
</body>

</html>