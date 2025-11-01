<?php

namespace App\Livewire;

use App\Models\Cashflow;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class HomeLivewire extends Component
{
    public $auth;

    public function mount()
    {
        $this->auth = Auth::user();
    }

    public function render()
    {
        $cashflows = Cashflow::where('user_id', $this->auth->id)->orderBy('created_at', 'desc')->get();
        $data = [
            'cashflows' => $cashflows
        ];
        return view('livewire.home-livewire', $data);
    }

// Add Cashflow
    public $addCashflowTitle;
    public $addCashflowTipe;     // <--- TAMBAHKAN INI
    public $addCashflowNominal;  // <--- TAMBAHKAN INI
    public $addCashflowDescription;

public function addCashflow()
    {
        $this->validate([
            'addCashflowTitle' => 'required|string|max:255',
            'addCashflowTipe' => 'required|in:pemasukan,pengeluaran', // <-- VALIDASI TAMBAHAN
            'addCashflowNominal' => 'required|numeric|min:0',        // <-- VALIDASI TAMBAHAN
            'addCashflowDescription' => 'nullable|string', // <-- Sebaiknya 'nullable' (opsional)
        ]);

        // Simpan cashflow ke database
        Cashflow::create([
            'title' => $this->addCashflowTitle,
            'tipe' => $this->addCashflowTipe, // Baris ini sekarang akan valid
            'nominal' => $this->addCashflowNominal, // Baris ini sekarang akan valid
            'description' => $this->addCashflowDescription,
            'user_id' => auth()->id(),
        ]);


        // Reset the form
        $this->reset([
            'addCashflowTitle', 
            'addCashflowTipe',     // <--- TAMBAHKAN DI RESET
            'addCashflowNominal',  // <--- TAMBAHKAN DI RESET
            'addCashflowDescription'
        ]);

        // Tutup modal
        $this->dispatch('closeModal', id: 'addCashflowModal');
    }

        // Simpan cashflow ke database
        Cashflow::create([
            'title' => $this->addCashflowTitle,
            'tipe' => $this->addCashflowTipe, // pemasukan / pengeluaran
            'nominal' => $this->addCashflowNominal,
            'description' => $this->addCashflowDescription,
            'user_id' => auth()->id(),
        ]);


        // Reset the form
        $this->reset(['addCashflowTitle', 'addCashflowDescription']);

        // Tutup modal
        $this->dispatch('closeModal', id: 'addCashflowModal');
    }

    // Edit Cashflow
    public $editCashflowId;
    public $editCashflowTitle;
    public $editCashflowTipe;
    public $editCashflowNominal;
    public $editCashflowDescription;

    public function prepareEditCashflow($id)
    {
    $cashflow = Cashflow::find($id);

    if (!$cashflow) {
        return;
    }

    $this->editCashflowId = $cashflow->id;
    $this->editCashflowTitle = $cashflow->title;
    $this->editCashflowTipe = $cashflow->tipe; // pemasukan/pengeluaran
    $this->editCashflowNominal = $cashflow->nominal;
    $this->editCashflowDescription = $cashflow->description;

    // tampilkan modal edit
    $this->dispatch('showModal', id: 'editCashflowModal');
    }


    public function editCashflow()
    {
    $this->validate([
        'editCashflowTitle' => 'required|string|max:255',
        'editCashflowTipe' => 'required|in:pemasukan,pengeluaran', // BENAR
        'editCashflowNominal' => 'required|numeric|min:0',
        'editCashflowDescription' => 'nullable|string|max:500',
    ]);

    $cashflow = Cashflow::find($this->editCashflowId);

    if (!$cashflow) {
        $this->addError('editCashflowTitle', 'Data cashflow tidak tersedia.');
        return;
    }

    $cashflow->title = $this->editCashflowTitle;
    $cashflow->tipe = $this->editCashflowTipe;
    $cashflow->nominal = $this->editCashflowNominal;
    $cashflow->description = $this->editCashflowDescription;
    $cashflow->save();

    // reset form input
    $this->reset([
        'editCashflowId',
        'editCashflowTitle',
        'editCashflowTipe',
        'editCashflowNominal',
        'editCashflowDescription',
    ]);

    // tutup modal edit
    $this->dispatch('closeModal', id: 'editCashflowModal');

    // beri notifikasi sukses
    session()->flash('message', 'Data cashflow berhasil diperbarui.');
    }


    // Delete Cashflow
    public $deleteCashflowId;
    public $deleteCashflowTitle;
    public $deleteCashflowConfirmTitle;

    public function prepareDeleteCashflow($id)
    {
    $cashflow = Cashflow::find($id);

    if (!$cashflow) {
        return;
    }

    $this->deleteCashflowId = $cashflow->id;
    $this->deleteCashflowTitle = $cashflow->title;
    $this->deleteCashflowTipe = $cashflow->tipe;
    $this->deleteCashflowNominal = $cashflow->nominal;

    // tampilkan modal konfirmasi hapus
    $this->dispatch('showModal', id: 'deleteCashflowModal');
    }


    public function deleteCashflow()
    {
    // Pastikan konfirmasi judul benar
    if ($this->deleteCashflowConfirmTitle !== $this->deleteCashflowTitle) {
        $this->addError(
            'deleteCashflowConfirmTitle',
            'Judul konfirmasi tidak sesuai dengan data cashflow yang akan dihapus.'
        );
        return;
    }

    // Pastikan data ada di database
    $cashflow = Cashflow::find($this->deleteCashflowId);
    if (!$cashflow) {
        $this->addError('deleteCashflowTitle', 'Data cashflow tidak ditemukan atau sudah dihapus.');
        return;
    }

    // Hapus data cashflow
    $cashflow->delete();

    // Reset semua properti delete
    $this->reset([
        'deleteCashflowId',
        'deleteCashflowTitle',
        'deleteCashflowTipe',
        'deleteCashflowNominal',
        'deleteCashflowConfirmTitle',
    ]);

    // Tutup modal
    $this->dispatch('closeModal', id: 'deleteCashflowModal');
    }
}