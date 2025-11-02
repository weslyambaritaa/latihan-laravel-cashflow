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
            
            <div class="d-flex align-items-center">
                {{-- Pastikan user sudah login sebelum menampilkan nama --}}
                @auth
                    <span class="navbar-text me-3">
                        Welcome, {{ auth()->user()->name }}
                    </span>
                    
                    {{-- Formulir Logout --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">
                            Logout
                        </button>
                    </form>
                @endauth
            </div>
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

    {{-- START: JavaScript Listener untuk Interaksi Livewire dengan Modal Bootstrap (PERBAIKAN UTAMA) --}}
    <script>
        document.addEventListener('livewire:initialized', () => {
            // Listener untuk membuka modal Bootstrap. Dipanggil dari CashflowLivewire.php (initEditModal/initDeleteModal)
            Livewire.on('openModal', ({ id }) => {
                // Pastikan library Bootstrap sudah dimuat dan Modal tersedia
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    // Coba dapatkan instance modal yang sudah ada (untuk menghindari duplikasi)
                    let modalElement = document.getElementById(id);
                    let modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
                    modal.show();
                } else {
                    console.error('Bootstrap Modal library not loaded.');
                }
            });

            // Listener untuk menutup modal Bootstrap. Dipanggil dari CashflowLivewire.php (editCashflow/deleteCashflow)
            Livewire.on('closeModal', ({ id }) => {
                let modalElement = document.getElementById(id);
                let modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
            });
        });
    </script>
    {{-- END: JavaScript Listener --}}
</body>

</html>
