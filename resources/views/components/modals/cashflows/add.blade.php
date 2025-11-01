{{-- Modal Add Cashflow --}}
<div class="modal fade" id="addCashflowModal" tabindex="-1" aria-labelledby="addCashflowModalLabel" aria-hidden="true"
    wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addCashflowModalLabel">Tambah Cashflow</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="addCashflow">
                    <div class="mb-3">
                        <label class="form-label">Judul Transaksi</label>
                        {{-- Input Judul --}}
                        <input type="text" class="form-control" wire:model="addCashflowTitle">
                        @error('addCashflowTitle')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jenis Transaksi</label>
                        {{-- Dropdown ini akan otomatis memilih 'pemasukan' --}}
                        <select class="form-select" wire:model="addCashflowTipe">
                            <option value="pemasukan">Pemasukan</option>
                            <option value="pengeluaran">Pengeluaran</option>
                        </select>
                        @error('addCashflowTipe')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nominal</label>
                        {{-- Input Nominal (TAMBAHKAN wire:model DI SINI) --}}
                        <input type="number" class="form-control" wire:model="addCashflowNominal">
                        @error('addCashflowNominal')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        {{-- Textarea Deskripsi (TAMBAHKAN wire:model DI SINI) --}}
                        <textarea class="form-control" rows="4" wire:model="addCashflowDescription"></textarea>
                        @error('addCashflowDescription')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>