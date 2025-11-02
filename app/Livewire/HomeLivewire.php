<?php

namespace App\Livewire;

use App\Models\Cashflow;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class HomeLivewire extends Component
{
    use WithFileUploads;

    public $auth;
    public $cashflows;
    public $cashflowTitle;
    public $cashflowTipe = 'pemasukan';
    public $cashflowNominal;
    public $cashflowDescription;
    public $cashflowFile;

    // --- START: Properti Baru untuk Edit dan Delete di Home ---
    public $selectedCashflowId;
    public $editCashflowTitle;
    public $editCashflowTipe;
    public $editCashflowNominal;
    public $editCashflowDescription;

    public $deleteCashflowTitle;
    public $deleteCashflowConfirmTitle;
    // --- END: Properti Baru ---

    public function mount()
    {
        $this->auth = Auth::user();
        $this->loadCashflows();
    }

    public function loadCashflows()
    {
        $this->cashflows = Cashflow::where('user_id', $this->auth->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function render()
    {
        return view('livewire.home-livewire');
    }

    // --- START: Logika CRUD Cashflow (Create) ---
    public function addCashflow()
    {
        $validated = $this->validate([
            'cashflowTitle' => 'required|string|max:255',
            'cashflowTipe' => 'required|in:pemasukan,pengeluaran',
            'cashflowNominal' => 'required|integer|min:1',
            'cashflowDescription' => 'nullable|string',
            'cashflowFile' => 'nullable|image|max:2048',
        ]);

        $path = null;
        if ($this->cashflowFile) {
            $userId = $this->auth->id;
            $dateNumber = now()->format('YmdHis');
            $extension = $this->cashflowFile->getClientOriginalExtension();
            $filename = $userId . '_' . $dateNumber . '.' . $extension;
            $path = $this->cashflowFile->storeAs('covers', $filename, 'public');
        }

        Cashflow::create([
            'user_id' => $this->auth->id,
            'title' => $validated['cashflowTitle'],
            'tipe' => ucfirst($validated['cashflowTipe']),
            'nominal' => $validated['cashflowNominal'],
            'description' => $validated['cashflowDescription'],
            'cover' => $path,
        ]);

        $this->reset(['cashflowTitle', 'cashflowNominal', 'cashflowDescription', 'cashflowFile']);
        $this->loadCashflows();
        $this->dispatch('closeModal', id: 'addCashflowModal');
    }
    // --- END: Logika CRUD Cashflow (Create) ---


    // --- START: Logika CRUD Cashflow (Edit & Delete) BARU ---

    // Dipanggil saat tombol "Ubah Data" ditekan di halaman Home
    public function initEditModal($cashflowId)
    {
        $cashflow = Cashflow::findOrFail($cashflowId);
        
        // Pengecekan keamanan
        if ($cashflow->user_id !== $this->auth->id) {
            return;
        }

        $this->selectedCashflowId = $cashflowId;
        $this->editCashflowTitle = $cashflow->title;
        $this->editCashflowTipe = strtolower($cashflow->tipe);
        $this->editCashflowNominal = $cashflow->nominal;
        $this->editCashflowDescription = $cashflow->description;

        $this->reset(['deleteCashflowConfirmTitle']);
        $this->dispatch('openModal', id: 'editCashflowModal');
    }

    public function editCashflow()
    {
        $validated = $this->validate([
            'editCashflowTitle' => 'required|string|max:255',
            'editCashflowTipe' => 'required|in:pemasukan,pengeluaran',
            'editCashflowNominal' => 'required|integer|min:1',
            'editCashflowDescription' => 'nullable|string',
        ]);

        $cashflow = Cashflow::findOrFail($this->selectedCashflowId);

        if ($cashflow->user_id !== $this->auth->id) {
            return;
        }
        
        $cashflow->title = $validated['editCashflowTitle'];
        $cashflow->tipe = ucfirst($validated['editCashflowTipe']);
        $cashflow->nominal = $validated['editCashflowNominal'];
        $cashflow->description = $validated['editCashflowDescription'];
        $cashflow->save();

        // Refresh data di halaman home dan tutup modal
        $this->loadCashflows();
        $this->dispatch('closeModal', id: 'editCashflowModal');
    }

    // Dipanggil saat tombol "Hapus" ditekan di halaman Home
    public function initDeleteModal($cashflowId)
    {
        $cashflow = Cashflow::findOrFail($cashflowId);

        if ($cashflow->user_id !== $this->auth->id) {
            return;
        }

        $this->selectedCashflowId = $cashflowId;
        $this->deleteCashflowTitle = $cashflow->title;
        $this->reset(['deleteCashflowConfirmTitle']);

        $this->dispatch('openModal', id: 'deleteCashflowModal');
    }

    public function deleteCashflow()
    {
        $cashflow = Cashflow::findOrFail($this->selectedCashflowId);

        // Pengecekan keamanan dan validasi konfirmasi judul
        if ($cashflow->user_id !== $this->auth->id) {
            return;
        }

        $this->validate([
            'deleteCashflowConfirmTitle' => 'required|in:' . $cashflow->title,
        ], [
            'deleteCashflowConfirmTitle.in' => 'Judul konfirmasi tidak cocok dengan judul Cashflow.',
            'deleteCashflowConfirmTitle.required' => 'Judul konfirmasi wajib diisi.'
        ]);

        // Hapus cover dan record
        if ($cashflow->cover && Storage::disk('public')->exists($cashflow->cover)) {
            Storage::disk('public')->delete($cashflow->cover);
        }

        $cashflow->delete();

        // Refresh data di halaman home dan tutup modal
        $this->loadCashflows();
        $this->dispatch('closeModal', id: 'deleteCashflowModal');
    }

    // --- END: Logika CRUD Cashflow (Edit & Delete) BARU ---
}