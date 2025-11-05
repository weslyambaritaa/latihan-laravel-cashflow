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

    {{-- TAMBAHAN: CDN ApexCharts --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    {{-- *** PERUBAHAN: Aset Trix Editor (CSS & JS) *** --}}
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
    <script type_5"text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
    {{-- *** END PERUBAHAN *** --}}

    @livewireStyles

    {{-- Opsional: Style untuk Trix agar rapi di Bootstrap --}}
    <style>
        /* Sembunyikan tombol upload file bawaan Trix jika tidak diperlukan */
        /* .trix-button-group--file-tools { display: none; } */

        /* Atur tinggi Trix Editor agar konsisten */
        trix-editor {
            min-height: 150px;
        }
        
        /* Fix untuk Bootstrap 5 */
        .trix-content {
            background-color: white;
        }
    </style>
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

            // --- Listener untuk SweetAlert (Toast) ---
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

            // --- TAMBAHAN: Logika ApexCharts ---
            let cashflowChart = null;
            const chartElement = document.getElementById('cashflowChart');
            
            // 1. Fungsi untuk inisialisasi/update chart
            const setupChart = (data) => {
                const options = {
                    series: data.series,
                    chart: {
                        type: 'bar',
                        height: 350,
                        toolbar: { show: true, tools: { download: false } }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '55%',
                            borderRadius: 4
                        },
                    },
                    dataLabels: { enabled: false },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    },
                    xaxis: {
                        categories: data.categories,
                    },
                    yaxis: {
                        title: { text: 'Rupiah (Rp)' }
                    },
                    fill: { opacity: 1 },
                    tooltip: {
                        y: {
                            // Format tooltip ke Rupiah
                            formatter: (val) => "Rp " + new Intl.NumberFormat('id-ID').format(val)
                        }
                    },
                    // Warna: Hijau (Pemasukan), Merah (Pengeluaran)
                    colors: ['#28a745', '#dc3545'] 
                };

                if (cashflowChart) {
                    // Jika chart sudah ada, update datanya
                    cashflowChart.updateOptions(options);
                } else {
                    // Jika belum, buat instance baru
                    cashflowChart = new ApexCharts(chartElement, options);
                    cashflowChart.render();
                }
            };

            // 2. Inisialisasi chart saat halaman load (jika elemen #cashflowChart ada)
            if (chartElement) {
                // Ambil data awal dari 'data-chart-data' yang kita tambahkan di home-livewire.blade.php
                try {
                    const initialData = JSON.parse(chartElement.dataset.chartData);
                    setupChart(initialData);
                } catch (e) {
                    console.error('Gagal mem-parsing data chart awal:', e);
                }
            }

            // 3. Listener untuk event 'updateChart' yang dikirim dari HomeLivewire.php
            Livewire.on('updateChart', ({ data }) => {
                if (chartElement) {
                    setupChart(data);
                }
            });
            // --- END: Logika ApexCharts ---
        });

        // --- Cek Flash Message (untuk notifikasi setelah redirect, cth: Hapus Data) ---
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