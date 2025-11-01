<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cashflow App</title>
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap-5.3.8-dist/css/bootstrap.min.css') }}">
    @livewireStyles
</head>

<body>
    {{-- Navbar --}}
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('app.home') }}">
                <img src="{{ asset('logo.png') }}" alt="Logo" width="30" height="30"
                    class="d-inline-block align-text-top">
                Cashflow
            </a>
            
            {{-- PERUBAHAN DI SINI --}}
            <div class="d-flex align-items-center">
                <span class="navbar-text me-3">
                    Welcome, {{ auth()->user()->name }}
                </span>
                
                {{-- TAMBAHKAN FORMULIR LOGOUT INI --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type"submit" class="btn btn-danger btn-sm">
                        Logout
                    </button>
                </form>
            </div>
            {{-- AKHIR PERUBAHAN --}}

        </div>
    </nav>

    {{-- Content --}}
    <main class="py-4">
        <div class="container">
            @yield('content')
        </div>
    </main>

    <script src="{{ asset('assets/vendor/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js') }}"></script>
    @livewireScripts
</body>

</html>