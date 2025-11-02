<?php

namespace App\Livewire;

use App\Models\Cashflow;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

// 1. NAMA KELAS DIPERBAIKI (agar cocok dengan nama file 'CashflowLivewire.php')
class CashflowLivewire extends Component
{
    use WithFileUploads;

    public $cashflow;
    public $auth;

    // --- Properti Baru untuk Edit dan Delete ---
    public $editCashflowTitle;
    public $editCashflowTipe;
    public $editCashflowNominal;
    public $editCashflowDescription;

    public $deleteCashflowTitle;
    public $deleteCashflowConfirmTitle;
    // --- END: Properti Baru ---

    // 2. FUNGSI MOUNT DIPERBAIKI
    // Ini akan secara otomatis menerima data $cashflow yang dikirim dari
    // @livewire('cashflow-livewire', ['cashflow' => $cashflow])
    public function mount(Cashflow $cashflow)
    {
        $this->auth = Auth::user();
        $this->cashflow = $cashflow;

        // 3. Pengecekan keamanan (memastikan ini milik user)
        if ($this->cashflow->user_id !== $this->auth->id) {
            // Jika bukan pemilik, tendang ke halaman home
            return redirect()->route('app.home');
        }
    }

    // 4. FUNGSI RENDER DIPERBAIKI
    public function render()
    {
        // Nama view harus 'livewire.cashflow-livewire'
        // agar cocok dengan file:
        // resources/views/livewire/cashflow-livewire.blade.php
        return view('livewire.cashflow-livewire');
    }

    // --- Logika Inisialisasi Modal ---

    // Dipanggil saat tombol "Ubah Data" ditekan
    public function initEditModal()
    {
        $this->editCashflowTitle = $this->cashflow->title;
        // Simpan dalam lowercase agar cocok dengan opsi di modal (pemasukan/pengeluaran)
        $this->editCashflowTipe = strtolower($this->cashflow->tipe);
        $this->editCashflowNominal = $this->cashflow->nominal;
        $this->editCashflowDescription = $this->cashflow->description;

        $this->reset(['deleteCashflowConfirmTitle']);

        // BARU: Dispatch event untuk membuka modal setelah data siap
        $this->dispatch('openModal', id: 'editCashflowModal');
    }

    // Dipanggil saat tombol "Hapus" ditekan
    public function initDeleteModal()
    {
        $this->deleteCashflowTitle = $this->cashflow->title;
        $this->reset(['deleteCashflowConfirmTitle']);

        // BARU: Dispatch event untuk membuka modal setelah data siap
        $this->dispatch('openModal', id: 'deleteCashflowModal');
    }

    // --- Logika Edit Cashflow ---
    public function editCashflow()
    {
        $validated = $this->validate([
            'editCashflowTitle' => 'required|string|max:255',
            'editCashflowTipe' => 'required|in:pemasukan,pengeluaran',
            'editCashflowNominal' => 'required|integer|min:1',
            'editCashflowDescription' => 'nullable|string',
        ]);

        $this->cashflow->title = $validated['editCashflowTitle'];
        // Pastikan 'tipe' disimpan dengan huruf kapital awal (Pemasukan/Pengeluaran)
        $this->cashflow->tipe = ucfirst($validated['editCashflowTipe']);
        $this->cashflow->nominal = $validated['editCashflowNominal'];
        $this->cashflow->description = $validated['editCashflowDescription'];
        $this->cashflow->save();

        // Kirim event untuk menutup modal
        $this->dispatch('closeModal', id: 'editCashflowModal');
    }

    // --- Logika Delete Cashflow ---
    public function deleteCashflow()
    {
        // Validasi konfirmasi judul
        $this->validate([
            'deleteCashflowConfirmTitle' => 'required|in:' . $this->cashflow->title,
        ], [
            'deleteCashflowConfirmTitle.in' => 'Judul konfirmasi tidak cocok dengan judul Cashflow.',
            'deleteCashflowConfirmTitle.required' => 'Judul konfirmasi wajib diisi.'
        ]);

        // Hapus cover jika ada
        if ($this->cashflow->cover && Storage::disk('public')->exists($this->cashflow->cover)) {
            Storage::disk('public')->delete($this->cashflow->cover);
        }

        $this->cashflow->delete();

        // Arahkan ke halaman home setelah berhasil dihapus
        return redirect()->route('app.home');
    }

    // --- (Logika Upload Cover Anda - Ini sudah terlihat benar) ---
    public $editCoverCashflowFile;

    public function editCoverCashflow()
    {
        $this->validate([
            'editCoverCashflowFile' => 'required|image|max:2048',  // 2MB Max
        ]);

        if ($this->editCoverCashflowFile) {
            // Hapus cover lama jika ada
            if ($this->cashflow->cover && Storage::disk('public')->exists($this->cashflow->cover)) {
                Storage::disk('public')->delete($this->cashflow->cover);
            }

            $userId = $this->auth->id;
            $dateNumber = now()->format('YmdHis');
            $extension = $this->editCoverCashflowFile->getClientOriginalExtension();
            $filename = $userId . '_' . $dateNumber . '.' . $extension;
            $path = $this->editCoverCashflowFile->storeAs('covers', $filename, 'public');
            $this->cashflow->cover = $path;
            $this->cashflow->save();
        }

        $this->reset(['editCoverCashflowFile']);

        $this->dispatch('closeModal', id: 'editCoverCashflowModal');
    }
}