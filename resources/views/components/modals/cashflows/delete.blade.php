<div class="modal fade" id="deleteCashflowModal" tabindex="-1" aria-labelledby="deleteCashflowModalLabel"
    aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="deleteCashflowModalLabel">Hapus Cashflow</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {{-- Form memanggil fungsi deleteCashflow() dari komponen Livewire yang sedang aktif --}}
            <form wire:submit.prevent="deleteCashflow">
                <div class="modal-body">
                    <p>
                        Anda yakin ingin menghapus Cashflow **<span
                                class="fw-bold">{{ $deleteCashflowTitle ?? '...' }}</span>**?
                        Tindakan ini tidak dapat dibatalkan.
                    </p>
                    <p class="text-danger">
                        Untuk konfirmasi, ketik judul Cashflow ini di bawah:
                    </p>
                    <div class="mb-3">
                        <input type="text"
                            class="form-control @error('deleteCashflowConfirmTitle') is-invalid @enderror"
                            placeholder="{{ $deleteCashflowTitle ?? 'Judul Cashflow' }}"
                            wire:model.live="deleteCashflowConfirmTitle">
                        @error('deleteCashflowConfirmTitle')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-danger"
                        {{ ($deleteCashflowConfirmTitle ?? '') !== ($deleteCashflowTitle ?? '') ? 'disabled' : '' }}>
                        Hapus Permanen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>