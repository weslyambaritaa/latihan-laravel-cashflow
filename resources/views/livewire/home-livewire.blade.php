<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="card-title">Daftar Cashflow</h3>
        {{-- Tombol Tambah --}}
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCashflowModal">
            Tambah
        </button>
    </div>

    {{-- Notifikasi Sukses --}}
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Daftar Cashflow --}}
    @if ($cashflows->isEmpty())
        <div class="alert alert-info" role="alert">
            Belum ada data cashflow.
        </div>
    @else
        <div class="list-group">
            @foreach ($cashflows as $cashflow)
                {{-- PERUBAHAN DIMULAI DI SINI --}}
                <div class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between align-items-center">

                        {{-- Sisi Kiri: Judul --}}
                        <div>
                            <h5 class="mb-0">{{ $cashflow->title }}</h5>
                        </div>

                        {{-- Sisi Kanan: Tipe, Nominal, & Tombol Aksi --}}
                        <div class="d-flex align-items-center">

                            {{-- Kolom Tipe (Sesuai Permintaan Anda) --}}
                            <div class="me-3" style="min-width: 100px; text-align: right;">
                                @if ($cashflow->tipe === 'pemasukan')
                                    <span class="text-success fw-bold">Pemasukan</span>
                                @elseif ($cashflow->tipe === 'pengeluaran')
                                    <span class="text-danger fw-bold">Pengeluaran</span>
                                @endif
                            </div>

                            {{-- Kolom Nominal (Seperti screenshot, teks hitam) --}}
                            <div class="me-3" style="min-width: 120px; text-align: right;">
                                <strong>
                                    Rp {{ number_format($cashflow->nominal, 0, ',', '.') }}
                                </strong>
                            </div>

                            {{-- Tombol Aksi (Edit, Delete, Detail) --}}
                            <div class="btn-group" role="group" aria-label="Aksi Cashflow">
                                {{-- Tombol Detail --}}
                                <a href="{{ route('app.cashflow.detail', $cashflow->id) }}"
                                    class="btn btn-info btn-sm">
                                    Detail
                                </a>

                                {{-- Tombol Edit --}}
                                <button class="btn btn-warning btn-sm"
                                    wire:click="prepareEditCashflow({{ $cashflow->id }})">
                                    Edit
                                </button>

                                {{-- Tombol Delete --}}
                                <button class="btn btn-danger btn-sm"
                                    wire:click="prepareDeleteCashflow({{ $cashflow->id }})">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- PERUBAHAN BERAKHIR DI SINI --}}
            @endforeach
        </div>
    @endif

    {{-- Modals --}}
    @include('components.modals.cashflows.add')
    @include('components.modals.cashflows.edit')
    @include('components.modals.cashflows.delete')
</div>