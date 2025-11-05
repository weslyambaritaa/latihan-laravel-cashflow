<div>
    {{-- 
      Sertakan Modal
      (Modal-modal ini tidak terlihat di halaman, tetapi perlu dimuat 
       agar Livewire dapat berinteraksi dengan mereka)
    --}}
    @include('components.modals.cashflows.edit')
    @include('components.modals.cashflows.delete')
    @include('components.modals.cashflows.edit-cover')

    {{-- Navigasi Breadcrumb --}}
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('app.home') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detail</li>
        </ol>
    </nav>

    {{-- Judul Halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Detail Cashflow</h3>
    </div>

    {{-- Kartu Detail --}}
    <div class="card shadow-sm">
        <div class="row g-0">
            {{-- Kolom Cover Gambar --}}
            <div class="col-md-4 bg-light p-3 text-center">
                @if ($cashflow->cover)
                    <img src="{{ asset('storage/' . $cashflow->cover) }}" 
                         class="img-fluid rounded" 
                         alt="Cover Image" 
                         style="max-height: 300px; object-fit: cover;">
                @else
                    {{-- Placeholder jika tidak ada gambar --}}
                    <img src="https://via.placeholder.com/400x300.png?text=Tidak+Ada+Cover" 
                         class="img-fluid rounded" 
                         alt="No Cover">
                @endif

                {{-- Tombol Ubah Cover --}}
                <button 
                    class="btn btn-outline-secondary btn-sm mt-3" 
                    data-bs-toggle="modal" 
                    data-bs-target="#editCoverCashflowModal">
                    Ubah Cover
                </button>
            </div>

            {{-- Kolom Detail Teks --}}
            <div class="col-md-8">
                <div class="card-body p-4">
                    {{-- Judul Transaksi --}}
                    <h4 class="card-title mb-1">{{ $cashflow->title }}</h4>

                    {{-- Tipe (Badge) dan Nominal --}}
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge fs-6 me-3 {{ $cashflow->tipe == 'pemasukan' ? 'bg-success' : 'bg-danger' }}">
                            {{ ucfirst($cashflow->tipe) }}
                        </span>
                        <span class="fs-5 fw-bold text-dark">
                            Rp {{ number_format($cashflow->nominal, 0, ',', '.') }}
                        </span>
                    </div>

                    {{-- Tanggal Dibuat --}}
                    <p class="card-text text-muted">
                        Dibuat pada: {{ $cashflow->created_at->format('d F Y, H:i') }}
                    </p>

                    <hr>

                    {{-- Area Deskripsi Trix --}}
                    <h6 class="fw-bold">Deskripsi:</h6>
                    
                    {{-- Kelas "trix-content" untuk styling --}}
                    <div class="trix-content">
                        {{-- Gunakan {!! ... !!} untuk merender HTML --}}
                        {!! $cashflow->description !!}
                    </div>
                    {{-- End Deskripsi --}}

                    {{-- Tombol Aksi (Ubah & Hapus) --}}
                    <div class="mt-4 pt-3 border-top">
                        <button wire:click="initEditModal" class="btn btn-warning">
                            <i class="bi bi-pencil-fill"></i> Ubah Data
                        </button>
                        <button wire:click="initDeleteModal" class="btn btn-danger">
                            <i class="bi bi-trash-fill"></i> Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>