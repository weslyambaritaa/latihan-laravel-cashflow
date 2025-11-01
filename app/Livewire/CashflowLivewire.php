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
