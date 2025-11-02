<?php

namespace App\Livewire;

use App\Models\Cashflow;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination; // 1. Tambahkan use WithPagination

class HomeLivewire extends Component
{
    use WithFileUploads;
    use WithPagination; // 2. Gunakan trait WithPagination

    protected $paginationTheme = 'bootstrap'; // 3. Atur tema pagination

    public $auth;
    
    // --- START: Properti untuk Pencarian dan Filter ---
    public $search = ''; 
    public $filterTipe = ''; 
    // --- END: Properti untuk Pencarian dan Filter ---
    
    // --- START: Properti untuk Total Akumulasi ---
    public $totalPemasukan = 0;
    public $totalPengeluaran = 0;
    public $totalAkumulasi = 0;
    // --- END: Properti untuk Total Akumulasi ---
    
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
        $this->loadTotals(); // 4. Panggil loadTotals() saat mount
    }

    // 5. Buat method terpisah untuk menghitung total akumulasi
    public function loadTotals()
    {
        // Perhitungan Total (Dihitung dari SEMUA data Cashflow user, tanpa filter)
        $allCashflows = Cashflow::where('user_id', $this->auth->id)->get();
        
        $this->totalPemasukan = $allCashflows->where('tipe', 'pemasukan')->sum('nominal');
        $this->totalPengeluaran = $allCashflows->where('tipe', 'pengeluaran')->sum('nominal');
        $this->totalAkumulasi = $this->totalPemasukan - $this->totalPengeluaran;
    }

    // 6. Modifikasi method render() untuk query dan pagination
    public function render()
    {
        $query = Cashflow::where('user_id', $this->auth->id);

        // Logika Pencarian: Berdasarkan title atau description
        if ($this->search) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Logika Filter Tipe: Berdasarkan 'pemasukan' atau 'pengeluaran'
        $tipe = strtolower($this->filterTipe);
        if (in_array($tipe, ['pemasukan', 'pengeluaran'])) {
            $query->where('tipe', $tipe);
        }

        // Ambil data dengan pagination (20 per halaman)
        $cashflows = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('livewire.home-livewire', [
            'cashflows' => $cashflows, // Pass data paginated ke view
        ]);
    }

    // 7. Modifikasi updated() untuk resetPage()
    public function updated($propertyName)
    {
        // Reset ke halaman 1 setiap kali filter atau search diubah
        if ($propertyName === 'search' || $propertyName === 'filterTipe') {
            $this->resetPage(); 
        }
    }

    // Logika CRUD Cashflow (Create)
    public function addCashflow()
    {
        $validated = $this->validate([
            'addCashflowTitle' => 'required|string|max:255',
            'addCashflowTipe' => 'required|in:pemasukan,pengeluaran',
            'addCashflowNominal' => 'required|integer|min:1',
            'addCashflowDescription' => 'nullable|string',
            'addCashflowFile' => 'nullable|image|max:2048',
        ]);

        $path = null;
        if ($this->addCashflowFile) {
            $userId = $this->auth->id;
            $dateNumber = now()->format('YmdHis');
            $extension = $this->addCashflowFile->getClientOriginalExtension();
            $filename = $userId . '_' . $dateNumber . '.' . $extension;
            $path = $this->addCashflowFile->storeAs('covers', $filename, 'public');
        }

        Cashflow::create([
            'user_id' => $this->auth->id,
            'title' => $validated['addCashflowTitle'],
            'tipe' => strtolower($validated['addCashflowTipe']), 
            'nominal' => $validated['addCashflowNominal'],
            'description' => $validated['addCashflowDescription'],
            'cover' => $path,
        ]);

        $this->reset(['addCashflowTitle', 'addCashflowTipe', 'addCashflowNominal', 'addCashflowDescription', 'addCashflowFile']);
        $this->loadTotals(); // 8. Panggil loadTotals() setelah create
        $this->dispatch('closeModal', id: 'addCashflowModal');
    }

    // --- Logika CRUD Cashflow (Edit & Delete) ---

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

        $this->loadTotals(); // 9. Panggil loadTotals() setelah edit
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

        $this->loadTotals(); // 10. Panggil loadTotals() setelah delete
        $this->dispatch('closeModal', id: 'deleteCashflowModal');
    }
}