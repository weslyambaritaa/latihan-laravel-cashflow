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
    
    // --- START: Properti untuk Add Cashflow (DISINKRONKAN DENGAN MODAL) ---
    public $addCashflowTitle;
    public $addCashflowTipe = 'pemasukan'; // Nilai default
    public $addCashflowNominal;
    public $addCashflowDescription;
    public $addCashflowFile;
    // --- END: Properti untuk Add Cashflow ---

    // --- Properti untuk Edit dan Delete (Sudah benar dari langkah sebelumnya) ---
    public $selectedCashflowId;
    public $editCashflowTitle;
    public $editCashflowTipe;
    public $editCashflowNominal;
    public $editCashflowDescription;

    public $deleteCashflowTitle;
    public $deleteCashflowConfirmTitle;

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

    // Logika CRUD Cashflow (Create)
    public function addCashflow()
    {
        // PERBAIKAN: Validasi menggunakan properti dengan awalan 'add'
        $validated = $this->validate([
            'addCashflowTitle' => 'required|string|max:255',
            'addCashflowTipe' => 'required|in:pemasukan,pengeluaran',
            'addCashflowNominal' => 'required|integer|min:1',
            'addCashflowDescription' => 'nullable|string',
            'addCashflowFile' => 'nullable|image|max:2048',
        ]);

        $path = null;
        // PERBAIKAN: Cek file menggunakan properti addCashflowFile
        if ($this->addCashflowFile) {
            $userId = $this->auth->id;
            $dateNumber = now()->format('YmdHis');
            // PERBAIKAN: Ambil ekstensi dari properti addCashflowFile
            $extension = $this->addCashflowFile->getClientOriginalExtension();
            $filename = $userId . '_' . $dateNumber . '.' . $extension;
            // PERBAIKAN: Simpan file dari properti addCashflowFile
            $path = $this->addCashflowFile->storeAs('covers', $filename, 'public');
        }

        Cashflow::create([
            'user_id' => $this->auth->id,
            'title' => $validated['addCashflowTitle'],
            // Menggunakan strtolower() untuk memenuhi ENUM database
            'tipe' => strtolower($validated['addCashflowTipe']), 
            'nominal' => $validated['addCashflowNominal'],
            'description' => $validated['addCashflowDescription'],
            'cover' => $path,
        ]);

        // PERBAIKAN: Reset properti dengan awalan 'add'
        $this->reset(['addCashflowTitle', 'addCashflowTipe', 'addCashflowNominal', 'addCashflowDescription', 'addCashflowFile']);
        $this->loadCashflows();
        $this->dispatch('closeModal', id: 'addCashflowModal');
    }

    // --- Logika CRUD Cashflow (Edit & Delete) tidak berubah dari langkah sebelumnya ---

    public function initEditModal($cashflowId)
    {
        $cashflow = Cashflow::findOrFail($cashflowId);
        
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
        $cashflow->tipe = strtolower($validated['editCashflowTipe']); 
        $cashflow->nominal = $validated['editCashflowNominal'];
        $cashflow->description = $validated['editCashflowDescription'];
        $cashflow->save();

        $this->loadCashflows();
        $this->dispatch('closeModal', id: 'editCashflowModal');
    }

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

        if ($cashflow->user_id !== $this->auth->id) {
            return;
        }

        $this->validate([
            'deleteCashflowConfirmTitle' => 'required|in:' . $cashflow->title,
        ], [
            'deleteCashflowConfirmTitle.in' => 'Judul konfirmasi tidak cocok dengan judul Cashflow.',
            'deleteCashflowConfirmTitle.required' => 'Judul konfirmasi wajib diisi.'
        ]);

        if ($cashflow->cover && Storage::disk('public')->exists($cashflow->cover)) {
            Storage::disk('public')->delete($cashflow->cover);
        }

        $cashflow->delete();

        $this->loadCashflows();
        $this->dispatch('closeModal', id: 'deleteCashflowModal');
    }
}