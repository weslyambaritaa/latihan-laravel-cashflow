<div>
    <h2 class="mt-3">Dashboard</h2>
    <div class="d-flex align-items-center mb-4">
        <h4 class="me-3 mb-0">List Cashflow</h4>
        <button class="btn btn-success" data-bs-target="#addCashflowModal" data-bs-toggle="modal">
            Tambah Data
        </button>
    </div>

    @if ($cashflows->isEmpty())
        <div class="alert alert-info">Belum ada data Cashflow. Silakan tambahkan data.</div>
    @else
        <div class="row">
            @foreach ($cashflows as $cashflow)
                <div class="col-md-4 mb-4">
                    <div class="card">
                        {{-- Cover Image --}}
                        @if ($cashflow->cover)
                            <img src="{{ asset('storage/' . $cashflow->cover) }}" class="card-img-top" alt="Cover"
                                style="height: 150px; object-fit: cover;">
                        @else
                            <div class="bg-secondary text-white d-flex align-items-center justify-content-center"
                                style="height: 150px;">
                                No Cover Image
                            </div>
                        @endif

                        <div class="card-body">
                            {{-- Tipe dan Judul --}}
                            <h5 class="card-title d-flex justify-content-between">
                                {{-- PERBAIKAN DI SINI: Mengubah ['cashflow' => ...] menjadi ['id' => ...] --}}
                                <a href="{{ route('app.cashflows.detail', ['id' => $cashflow->id]) }}" 
                                    class="text-decoration-none text-body">
                                    {{ $cashflow->title }}
                                </a>
                                @if ($cashflow->tipe === 'Pemasukan')
                                    <span class="badge bg-success">Pemasukan</span>
                                @elseif ($cashflow->tipe === 'Pengeluaran')
                                    <span class="badge bg-danger">Pengeluaran</span>
                                @endif
                            </h5>

                            {{-- Nominal --}}
                            <p class="card-text text-muted mb-3">
                                Rp {{ number_format($cashflow->nominal, 0, ',', '.') }}
                            </p>

                            {{-- Tombol Aksi --}}
                            <div class="d-flex gap-2">
                                {{-- Tombol Edit --}}
                                <button class="btn btn-primary btn-sm flex-grow-1"
                                    wire:click="initEditModal({{ $cashflow->id }})">
                                    Ubah Data
                                </button>
                                {{-- Tombol Delete --}}
                                <button class="btn btn-danger btn-sm flex-grow-1"
                                    wire:click="initDeleteModal({{ $cashflow->id }})">
                                    Hapus
                                </button>
                                {{-- Tombol Detail --}}
                                <a href="{{ route('app.cashflows.detail', ['id' => $cashflow->id]) }}"
                                    class="btn btn-info btn-sm">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Modals --}}
    @include('components.modals.cashflows.add')
    @include('components.modals.cashflows.edit')
    @include('components.modals.cashflows.delete')

</div>