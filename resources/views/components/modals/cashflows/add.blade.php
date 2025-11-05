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
                        {{-- Input Nominal (SUDAH BENAR) --}}
                        <input type="number" class="form-control" wire:model="addCashflowNominal">
                        @error('addCashflowNominal')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- *** PERUBAHAN: Textarea Deskripsi diganti Trix Editor *** --}}
                    <div class="mb-3"
                        wire:ignore
                        {{-- Inisialisasi AlpineJS dan binding ke Livewire --}}
                        x-data="{
                            content: @entangle('addCashflowDescription'),
                            init() {
                                let editor = this.$refs.trixEditorAdd;

                                // 1. Set nilai Trix saat modal dibuka (biasanya kosong)
                                if (editor.editor) {
                                    editor.editor.loadHTML(this.content || '');
                                } else {
                                    // Fallback jika Trix belum siap
                                    editor.addEventListener('trix-initialize', () => {
                                        editor.editor.loadHTML(this.content || '');
                                    });
                                }
                                
                                // 2. Update properti Livewire (content) saat Trix diubah
                                editor.addEventListener('trix-change', () => {
                                    this.content = editor.value;
                                });

                                // 3. Pantau 'content'. Jika Livewire meresetnya (cth: setelah simpan), update Trix
                                this.$watch('content', (newValue) => {
                                    if (newValue !== editor.value) {
                                        editor.editor.loadHTML(newValue || '');
                                    }
                                });
                            }
                        }"
                    >
                        <label class="form-label" for="addCashflowDescription_input">Deskripsi</label>
                        
                        {{-- Input 'hidden' ini diperlukan Trix. ID harus unik dan cocok dengan 'input' di trix-editor --}}
                        <input id="addCashflowDescription_input" type="hidden">
                        
                        {{-- Trix Editor --}}
                        <trix-editor
                            x-ref="trixEditorAdd"
                            input="addCashflowDescription_input"
                            class="form-control @error('addCashflowDescription') is-invalid @enderror">
                        </trix-editor>
                        
                        {{-- Tampilkan error (jika ada) --}}
                        @error('addCashflowDescription')
                            <span class="text-danger mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    {{-- *** END PERUBAHAN *** --}}

                    {{-- TAMBAHAN: Input file untuk cover --}}
                    <div class="mb-3">
                        <label class="form-label">Cover (Opsional)</label>
                        <input type="file" class="form-control" wire:model="addCashflowFile">
                        @error('addCashflowFile')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        {{-- TAMBAHAN: wire:loading.attr="disabled" untuk mencegah double-click --}}
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>