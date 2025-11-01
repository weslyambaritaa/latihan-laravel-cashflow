<div class="mt-3">
    <div class="card">
        <div class="card-header d-flex">
            <div class="flex-fill">
                <h3>Hay, {{ $auth->name }}</h3>
            </div>
            <div>
                <a href="{{ route('auth.logout') }}" class="btn btn-warning">Keluar</a>
            </div>
        </div>
        <div class="card-body">
            <div class="d-flex mb-2">
                <div class="flex-fill">
                    <h3>Daftar Cashflow</h3>
                </div>
                <div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCashflowModal">
                        Tambah Cashflow
                    </button>
                </div>
            </div>
            <table class="table table-striped">
                <tr class="table-light">
                    <th>No</th>
                    <th>Judul</th>
                    <th>Dibuat pada</th>
                    <th>Diubah pada</th>
                    <th>Status</th>
                    <th>Tindakan</th>
                </tr>
                @foreach ($cashflows as $key => $cashflow)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $cashflow->title }}</td>
                        <td>{{ date('d F Y, H:i', strtotime($cashflow->created_at)) }}</td>
                        <td>{{ date('d F Y, H:i', strtotime($cashflow->updated_at)) }}</td>
                        <td>
                            @if ($cashflow->is_finished)
                                <span class="badge bg-success">Selesai</span>
                            @else
                                <span class="badge bg-danger">Belum selesai</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('app.cashflows.detail', ['cashflow_id' => $cashflow->id]) }}"
                                class="btn btn-sm btn-info">
                                Detail
                            </a>
                            <button wire:click="prepareEditCashflow({{ $cashflow->id }})" class="btn btn-sm btn-warning">
                                Edit
                            </button>
                            <button wire:click="prepareDeleteCashflow({{ $cashflow->id }})" class="btn btn-sm btn-danger">
                                Hapus
                            </button>
                        </td>
                    </tr>
                @endforeach
                @if (sizeof($cashflows) === 0)
                    <tr>
                        <td colspan="6" class="text-center">Belum ada data cashflow yang tersedia.</td>
                    </tr>
                @endif
            </table>
        </div>
    </div>

    {{-- Modals --}}
    @include('components.modals.cashflows.add')
    @include('components.modals.cashflows.edit')
    @include('components.modals.cashflows.delete')
</div>