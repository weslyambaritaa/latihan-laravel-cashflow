<div>
    <h2 class="mt-3 display-6 fw-bold text-dark border-bottom pb-2">Dashboard Keuangan <i class="bi bi-graph-up-arrow"></i></h2>
    
    {{-- START: Summary Card Total Cashflow (Lebih Elegan) --}}
    <div class="row mb-5 mt-4">
        
        {{-- Total Pemasukan --}}
        <div class="col-md-4 mb-4">
            <div class="card bg-white border-success border-2 shadow-lg rounded-4 overflow-hidden h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-arrow-up-circle-fill text-success fs-2 me-3"></i>
                        <div>
                            <p class="text-uppercase text-secondary fw-bold mb-0">Total Pemasukan</p>
                            <p class="fs-4 fw-bolder text-success mb-0">
                                Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Pengeluaran --}}
        <div class="col-md-4 mb-4">
            <div class="card bg-white border-danger border-2 shadow-lg rounded-4 overflow-hidden h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-arrow-down-circle-fill text-danger fs-2 me-3"></i>
                        <div>
                            <p class="text-uppercase text-secondary fw-bold mb-0">Total Pengeluaran</p>
                            <p class="fs-4 fw-bolder text-danger mb-0">
                                Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Akumulasi --}}
        @php
            $akumulasiColor = $totalAkumulasi >= 0 ? 'text-primary' : 'text-secondary';
        @endphp
        <div class="col-md-4 mb-4">
            <div class="card bg-white border-primary border-2 shadow-lg rounded-4 overflow-hidden h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-wallet-fill {{ $akumulasiColor }} fs-2 me-3"></i>
                        <div>
                            <p class="text-uppercase text-secondary fw-bold mb-0">Akumulasi Total</p>
                            <p class="fs-4 fw-bolder {{ $akumulasiColor }} mb-0">
                                Rp {{ number_format($totalAkumulasi, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- END: Summary Card Total Cashflow --}}

    {{-- START: TAMBAHAN STATISTIK CHART --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm rounded-4 border-0">
                <div class="card-body">
                    <h5 class="card-title text-dark fw-bold">Statistik 7 Hari Terakhir</h5>
                    {{-- 
                        Container untuk chart. 
                        'data-chart-data' digunakan untuk mengirim data awal 
                        dari $chartData (PHP) ke JavaScript (di app.blade.php)
                    --}}
                    <div id="cashflowChart" data-chart-data="{{ json_encode($chartData) }}"></div>
                </div>
            </div>
        </div>
    </div>
    {{-- END: TAMBAHAN STATISTIK CHART --}}


    {{-- START: Area Kontrol (Tambah Data, Filter, Search) --}}
    <div class="d-flex flex-column-reverse flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        
        {{-- Tombol Tambah Data dan Jumlah Data --}}
        <div class="order-1 order-md-0">
            {{-- 1. Gunakan $cashflows->total() untuk jumlah total data (bukan $cashflows->count()) --}}
            <h4 class="me-3 mb-0 text-dark">List Transaksi ({{ $cashflows->total() }} Data)</h4>
            <button class="btn btn-success mt-2 shadow-sm fw-bold rounded-pill px-4" data-bs-target="#addCashflowModal" data-bs-toggle="modal">
                <i class="bi bi-plus-lg"></i> Tambah Transaksi
            </button>
        </div>

        {{-- Kolom Filter dan Search (di sebelah KANAN pada md ke atas) --}}
        <div class="d-flex flex-column flex-sm-row gap-2 order-0 order-md-1 w-100 w-md-50">
            {{-- Filter Tipe --}}
            <select class="form-select w-sm-50 shadow-sm rounded-pill" wire:model.live="filterTipe">
                <option value="">Semua Tipe</option>
                <option value="pemasukan">Pemasukan</option>
                <option value="pengeluaran">Pengeluaran</option>
            </select>
            
            {{-- Pencarian --}}
            <input type="text" class="form-control w-sm-50 shadow-sm rounded-pill" placeholder="Cari judul/deskripsi..."
                wire:model.live="search">
        </div>
    </div>
    {{-- END: Area Kontrol --}}

    @if ($cashflows->isEmpty())
        <div class="alert alert-info border-0 shadow-sm rounded-3">Tidak ada data Cashflow yang ditemukan.</div>
    @else
        <div class="row">
            @foreach ($cashflows as $cashflow)
                {{-- Card per Cashflow --}}
                <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                    
                    {{-- Penentuan Warna Card --}}
                    @php
                        $isPemasukan = strtolower($cashflow->tipe) === 'pemasukan';
                        $colorClass = $isPemasukan ? 'text-success' : 'text-danger';
                        $badgeColor = $isPemasukan ? 'bg-success' : 'bg-danger';
                        $borderColor = $isPemasukan ? 'border-success' : 'border-danger';
                    @endphp

                    {{-- Card Utama dengan efek Shadow & Hover --}}
                    <div class="card h-100 shadow-lg border-0 rounded-4 overflow-hidden hover-scale {{ $borderColor }}"
                        style="transition: transform 0.3s, box-shadow 0.3s;"
                        onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 1rem 3rem rgba(0,0,0,.175)'"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 .5rem 1rem rgba(0,0,0,.15)'">
                        
                        {{-- Area Cover Image --}}
                        <div class="overflow-hidden" style="height: 120px; position: relative; background-color: #f8f9fa;">
                            @if ($cashflow->cover)
                                <img src="{{ asset('storage/'. $cashflow->cover) }}" class="w-100 h-100 object-fit-cover" alt="Cover">
                            @else
                                <div class="text-muted d-flex align-items-center justify-content-center w-100 h-100 {{ $isPemasukan ? 'bg-success-subtle' : 'bg-danger-subtle' }}">
                                    <small class="text-uppercase fw-bold"><i class="bi bi-image-fill me-1"></i> No Cover</small>
                                </div>
                            @endif
                            
                            {{-- Badge Tipe di Atas Gambar --}}
                            <span class="badge {{ $badgeColor }} position-absolute top-0 end-0 mt-2 me-2 shadow fw-bold rounded-pill">
                                <i class="bi bi-arrow-{{ $isPemasukan ? 'up' : 'down' }}-circle-fill"></i> {{ $cashflow->tipe }}
                            </span>
                        </div>

                        <div class="card-body d-flex flex-column">
                            
                            {{-- Judul Cashflow --}}
                            <h5 class="card-title mb-1 text-dark">
                                {{-- PERBAIKAN DI SINI: 'id'B' diubah menjadi 'id' --}}
                                <a href="{{ route('app.cashflows.detail', ['id' => $cashflow->id]) }}" 
                                    class="text-decoration-none text-body fw-bolder text-truncate d-block"
                                    title="{{ $cashflow->title }}">
                                    {{ $cashflow->title }}
                                </a>
                            </h5>

                            {{-- Nominal dengan Warna Kontras --}}
                            <p class="card-text mb-2 {{ $colorClass }} fw-bolder fs-4">
                                Rp {{ number_format($cashflow->nominal, 0, ',', '.') }}
                            </p>
                            
                            {{-- Keterangan Singkat (Truncated) --}}
                            <p class="card-text text-muted small mb-3 text-truncate">
                                {{ $cashflow->description ?: 'Tidak ada deskripsi transaksi.' }}
                            </p> {{-- <-- PERBAIKAN DI SINI: </label> diubah menjadi </p> --}}
                            
                            {{-- Tanggal Pembuatan dan Perubahan --}}
                            <div class="small text-end text-secondary mb-3 pt-2 border-top">
                                <p class="mb-0" title="Tanggal Pembuatan">
                                    <i class="bi bi-calendar-plus-fill me-1"></i> Dibuat: {{ $cashflow->created_at->format('d M Y') }}
                                </p>
                                @if($cashflow->created_at != $cashflow->updated_at)
                                    <p class="mb-0 text-primary" title="Terakhir Diubah">
                                        <i class="bi bi-calendar-check-fill me-1"></i> Ubah: {{ $cashflow->updated_at->format('d M Y') }}
                                    </p>
                                @endif
                            </div>


                            {{-- Tombol Aksi (3 tombol, menggunakan flex-fill) --}}
                            <div class="mt-auto d-flex gap-2">
                                {{-- Tombol Edit --}}
                                <button class="btn btn-outline-primary btn-sm flex-fill fw-bold"
                                    wire:click="initEditModal({{ $cashflow->id }})" title="Ubah Data">
                                    <i class="bi bi-pencil"></i> Ubah
                                </button>
                                
                                {{-- Tombol Delete --}}
                                <button class="btn btn-outline-danger btn-sm flex-fill fw-bold"
                                    wire:click="initDeleteModal({{ $cashflow->id }})" title="Hapus Data">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>

                                {{-- Tombol Detail --}}
                                <a href="{{ route('app.cashflows.detail', ['id' => $cashflow->id]) }}"
                                    class="btn btn-info btn-sm text-white fw-bold" title="Lihat Detail">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- 2. Tambahkan link pagination di bawah baris --}}
        <div class="mt-4 d-flex justify-content-center">
            {{ $cashflows->links() }}
        </div>

    @endif

    {{-- Modals --}}
    @include('components.modals.cashflows.add')
    @include('components.modals.cashflows.edit')
    @include('components.modals.cashflows.delete')
</div>