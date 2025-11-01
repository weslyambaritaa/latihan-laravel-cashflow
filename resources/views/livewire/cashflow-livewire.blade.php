<div class="mt-3">
    <div class="card">
        <div class="card-header d-flex">
            <div class="flex-fill">
                <a href="{{ route('app.home') }}" class="text-decoration-none">
                    <small class="text-muted">
                        &lt; Kembali
                    </small>
                </a>
                <h3>
                    {{ $cashflow->title }}
                    @if ($cashflow->tipe === 'Pemasukan')
                        <small class="badge bg-success">Pemasukan</small>
                    @elseif ($cashflow->tipe === 'Pengeluaran')
                        <small class="badge bg-danger">Pengeluaran</small>
                    @endif
                </h3>
                <p class="text-muted mb-0">
                    <strong>Nominal:</strong> Rp {{ number_format($cashflow->nominal, 0, ',', '.') }}
                </p>
            </div>
            <div>
                <button class="btn btn-warning" data-bs-target="#editCoverCashflowModal" data-bs-toggle="modal">
                    Ubah Cover
                </button>
            </div>
        </div>

        <div class="card-body">
            @if ($cashflow->cover)
                <img src="{{ asset('storage/' . $cashflow->cover) }}" alt="Cover" style="max-width: 100%;">
                <hr>
            @endif
            <p style="font-size: 18px;">{{ $cashflow->description }}</p>
        </div>
    </div>

    {{-- Modals --}}
    @include('components.modals.cashflows.edit-cover')
</div>
