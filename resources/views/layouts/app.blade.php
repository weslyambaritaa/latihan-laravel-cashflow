<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cashflow App</title>
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap-5.3.8-dist/css/bootstrap.min.css') }}">
    
    {{-- TAMBAHAN: CDN SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
                {{-- Hanya tampilkan jika user sudah login --}}
                @auth
                    <span class="navbar-text me-3">
                        Welcome, {{ auth()->user()->name }}
                    </span>
                    
                    {{-- Formulir Logout (Memastikan type="submit" untuk POST request) --}}
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

    {{-- START: JavaScript Listener untuk Interaksi Livewire --}}
    <script>
        document.addEventListener('livewire:initialized', () => {
            // Listener untuk membuka modal Bootstrap.
            Livewire.on('openModal', ({ id }) => {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    let modalElement = document.getElementById(id);
                    let modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
                    modal.show();
                } else {
                    console.error('Bootstrap Modal library not loaded.');
                }
            });

            // Listener untuk menutup modal Bootstrap.
            Livewire.on('closeModal', ({ id }) => {
                let modalElement = document.getElementById(id);
                let modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
            });

            // --- TAMBAHAN: Listener untuk SweetAlert (Toast) ---
            // Listener ini akan menangkap event 'swal:alert'
            Livewire.on('swal:alert', ({ icon, title, text }) => {
                Swal.fire({
                    icon: icon,       // 'success', 'error', 'warning', 'info'
                    title: title,     // Judul popup
                    text: text,       // Teks di bawah judul
                    toast: true,      // Tampilkan sebagai toast
                    position: 'top-end', // Posisi di kanan atas
                    showConfirmButton: false, // Sembunyikan tombol OK
                    timer: 3000,      // Tutup otomatis setelah 3 detik
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        // Untuk pause timer saat mouse hover
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    }
                });
            });
            // --- END: Listener SweetAlert ---
        });

        // --- TAMBAHAN: Cek Flash Message (untuk notifikasi setelah redirect, cth: Hapus Data) ---
        // Ini dijalankan saat halaman di-load, bukan saat Livewire inisialisasi
        @if (session()->has('message'))
            Swal.fire({
                icon: '{{ session('message-icon', 'success') }}', // 'success' adalah default
                title: '{{ session('message') }}',
                showConfirmButton: false,
                timer: 3000
            });
        @endif
        // --- END: Cek Flash Message ---
    </script>
    {{-- END: JavaScript Listener --}}
</body>

</html>