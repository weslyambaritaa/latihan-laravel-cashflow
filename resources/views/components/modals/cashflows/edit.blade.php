<div class="modal fade" id="editCashflowModal" tabindex="-1" aria-labelledby="editCashflowModalLabel"
    aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editCashflowModalLabel">Ubah Data Cashflow</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {{-- Form memanggil fungsi editCashflow() dari komponen Livewire yang sedang aktif --}}
            <form wire:submit.prevent="editCashflow">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editCashflowTitle" class="form-label">Judul Cashflow</label>
                        {{-- Menggunakan properti editCashflowTitle --}}
                        <input type="text" class="form-control @error('editCashflowTitle') is-invalid @enderror"
                            id="editCashflowTitle" wire:model="editCashflowTitle">
                        @error('editCashflowTitle')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="editCashflowTipe" class="form-label">Tipe</label>
                        {{-- Menggunakan properti editCashflowTipe --}}
                        <select class="form-select @error('editCashflowTipe') is-invalid @enderror"
                            id="editCashflowTipe" wire:model="editCashflowTipe">
                            <option value="pemasukan">Pemasukan</option>
                            <option value="pengeluaran">Pengeluaran</option>
                        </select>
                        @error('editCashflowTipe')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="editCashflowNominal" class="form-label">Nominal (Rp)</label>
                        {{-- Menggunakan properti editCashflowNominal --}}
                        <input type="number" class="form-control @error('editCashflowNominal') is-invalid @enderror"
                            id="editCashflowNominal" wire:model="editCashflowNominal">
                        @error('editCashflowNominal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="editCashflowDescription" class="form-label">Deskripsi</label>
                        {{-- Menggunakan properti editCashflowDescription --}}
                        <textarea class="form-control @error('editCashflowDescription') is-invalid @enderror" id="editCashflowDescription"
                            rows="3" wire:model="editCashflowDescription"></textarea>
                        @error('editCashflowDescription')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>