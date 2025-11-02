<?php

namespace App\Livewire;

use App\Models\Cashflow;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class CashflowLivewire extends Component
{
    use WithFileUploads;

    public $cashflow;
    public $auth;

    // --- Properti untuk Edit dan Delete ---
    public $editCashflowTitle;
    public $editCashflowTipe;
    public $editCashflowNominal;
    public $editCashflowDescription;

    public $deleteCashflowTitle;
    public $deleteCashflowConfirmTitle;
    
    // Properti untuk Upload Cover
    public $editCoverCashflowFile;


    public function mount(Cashflow $cashflow)
    {
        $this->auth = Auth::user();
        $this->cashflow = $cashflow;

        if ($this->cashflow->user_id !== $this->auth->id) {
            return redirect()->route('app.home');
        }
    }

    public function render()
    {
        return view('livewire.cashflow-livewire');
    }

    // --- Logika Inisialisasi Modal ---

    public function initEditModal()
    {
        $this->editCashflowTitle = $this->cashflow->title;
        $this->editCashflowTipe = strtolower($this->cashflow->tipe);
        $this->editCashflowNominal = $this->cashflow->nominal;
        $this->editCashflowDescription = $this->cashflow->description;
        $this->reset(['deleteCashflowConfirmTitle']);
        $this->dispatch('openModal', id: 'editCashflowModal');
    }

    public function initDeleteModal()
    {
        $this->deleteCashflowTitle = $this->cashflow->title;
        $this->reset(['deleteCashflowConfirmTitle']);
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
        $this->cashflow->tipe = strtolower($validated['editCashflowTipe']);
        $this->cashflow->nominal = $validated['editCashflowNominal'];
        $this->cashflow->description = $validated['editCashflowDescription'];
        $this->cashflow->save();

        $this->dispatch('closeModal', id: 'editCashflowModal');

        // --- TAMBAHAN: Kirim event SweetAlert ---
        $this->dispatch('swal:alert', [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Data cashflow berhasil diperbarui.',
        ]);
    }

    // --- Logika Delete Cashflow ---
    public function deleteCashflow()
    {
        $this->validate([
            'deleteCashflowConfirmTitle' => 'required|in:' . $this->cashflow->title,
        ], [
            'deleteCashflowConfirmTitle.in' => 'Judul konfirmasi tidak cocok.',
            'deleteCashflowConfirmTitle.required' => 'Judul konfirmasi wajib diisi.'
        ]);

        if ($this->cashflow->cover && Storage::disk('public')->exists($this->cashflow->cover)) {
            Storage::disk('public')->delete($this->cashflow->cover);
        }

        $this->cashflow->delete();

        // --- PERUBAHAN: Gunakan session flash untuk notifikasi redirect ---
        session()->flash('message', 'Cashflow berhasil dihapus.');
        session()->flash('message-icon', 'success'); // Anda bisa ganti 'error' jika gagal

        // Arahkan ke halaman home setelah berhasil dihapus
        return redirect()->route('app.home');
    }

    // --- (Logika Upload Cover) ---
    public function editCoverCashflow()
    {
        $this->validate([
            'editCoverCashflowFile' => 'required|image|max:2048',  // 2MB Max
        ]);

        if ($this->editCoverCashflowFile) {
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

        // --- TAMBAHAN: Kirim event SweetAlert ---
        $this->dispatch('swal:alert', [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Cover cashflow berhasil diperbarui.',
        ]);
    }
}