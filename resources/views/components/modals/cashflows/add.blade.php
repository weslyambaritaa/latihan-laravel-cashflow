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
                        {{-- PERBAIKAN: Tambahkan 'for' dan 'id' --}}
                        <label class="form-label" for="addCashflowTitle">Judul Transaksi</label>
                        <input type="text" class="form-control" id="addCashflowTitle" wire:model="addCashflowTitle">
                        @error('addCashflowTitle')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        {{-- PERBAIKAN: Tambahkan 'for' dan 'id' --}}
                        <label class="form-label" for="addCashflowTipe">Jenis Transaksi</label>
                        <select class="form-select" id="addCashflowTipe" wire:model="addCashflowTipe">
                            <option value="pemasukan">Pemasukan</option>
                            <option value="pengeluaran">Pengeluaran</option>
                        </select>
                        @error('addCashflowTipe')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        {{-- PERBAIKAN: Tambahkan 'for' dan 'id' --}}
                        <label class="form-label" for="addCashflowNominal">Nominal</label>
                        <input type="number" class="form-control" id="addCashflowNominal" wire:model="addCashflowNominal">
                        @error('addCashflowNominal')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Bagian Trix Editor (Seharusnya sudah benar) --}}
                    <div class="mb-3"
                        wire:ignore
                        x-data="{
                            content: @entangle('addCashflowDescription'),
                            init() {
                                let editor = this.$refs.trixEditorAdd;

                                editor.addEventListener('trix-initialize', () => {
                                    editor.editor.loadHTML(this.content || '');
                                });
                                
                                editor.addEventListener('trix-change', () => {
                                    this.content = editor.value;
                                });

                                this.$watch('content', (newValue) => {
                                    if (newValue !== editor.value) {
                                        editor.editor.loadHTML(newValue || '');
                                    }
                                });
                            }
                        }"
                    >
                        {{-- Label 'for' ini sudah merujuk ke 'input' di bawah --}}
                        <label class="form-label" for="addCashflowDescription_input">Deskripsi</label>
                        
                        <input id="addCashflowDescription_input" type="hidden">
                        
                        <trix-editor
                            x-ref="trixEditorAdd"
                            input="addCashflowDescription_input"
                            class="form-control @error('addCashflowDescription') is-invalid @enderror">
                        </trix-editor>
                        
                        @error('addCashflowDescription')
                            <span class="text-danger mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    {{-- End Trix Editor --}}

                    {{-- 
                      *** DIHAPUS ***
                      Blok input file cover telah dihapus dari sini.
                    --}}

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>