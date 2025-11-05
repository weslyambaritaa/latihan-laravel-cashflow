<?php

namespace App\Livewire;

use App\Models\Cashflow;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class HomeLivewire extends Component
{
    use WithFileUploads;
    use WithPagination;

    // Menetapkan theme pagination untuk komponen Livewire ini ke 'bootstrap'
    protected $paginationTheme = 'bootstrap';


    // Properti untuk Modal Tambah
    public $addCashflowTitle;
    public $addCashflowTipe = 'pemasukan'; // Default
    public $addCashflowNominal;
    public $addCashflowDescription;
    // public $addCashflowFile; // <-- DIHAPUS

    // Properti untuk Modal Edit
    public $editCashflowId;
    public $editCashflowTitle;
    public $editCashflowTipe;
    public $editCashflowNominal;
    public $editCashflowDescription;

    // Properti untuk Modal Delete
    public $deleteCashflowId;
    public $deleteCashflowTitle;
    public $deleteCashflowConfirmTitle;

    // Properti untuk Filter dan Search
    public $search = '';
    public $filterTipe = '';


    // Method untuk Reset Pagination saat Search
    public function updatedSearch()
    {
        $this->resetPage();
    }

    // Method untuk Reset Pagination saat Filter
    public function updatedFilterTipe()
    {
        $this->resetPage();
    }


    public function render()
    {
        $auth = Auth::user();

        // --- Query untuk Statistik Total ---
        $baseQuery = Cashflow::where('user_id', $auth->id);

        $totalPemasukan = (clone $baseQuery)->where('tipe', 'pemasukan')->sum('nominal');
        $totalPengeluaran = (clone $baseQuery)->where('tipe', 'pengeluaran')->sum('nominal');
        $totalAkumulasi = $totalPemasukan - $totalPengeluaran;
        
        // --- Query untuk Statistik Chart 7 Hari Terakhir ---
        $startDate = Carbon::now()->subDays(6)->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        
        $chartDataPemasukan = (clone $baseQuery)->where('tipe', 'pemasukan')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(nominal) as total')
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get()
            ->pluck('total', 'date');

        $chartDataPengeluaran = (clone $baseQuery)->where('tipe', 'pengeluaran')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(nominal) as total')
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get()
            ->pluck('total', 'date');

        $chartCategories = [];
        $chartSeriesPemasukan = [];
        $chartSeriesPengeluaran = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $startDate->copy()->addDays($i);
            $dateString = $date->format('Y-m-d');
            
            $chartCategories[] = $date->format('d M'); 
            
            $chartSeriesPemasukan[] = $chartDataPemasukan->get($dateString, 0);
            $chartSeriesPengeluaran[] = $chartDataPengeluaran->get($dateString, 0);
        }

        $chartData = [
            'series' => [
                ['name' => 'Pemasukan', 'data' => $chartSeriesPemasukan],
                ['name' => 'Pengeluaran', 'data' => $chartSeriesPengeluaran]
            ],
            'categories' => $chartCategories
        ];


        // --- Query untuk List Transaksi (dengan filter dan search) ---
        $cashflowsQuery = Cashflow::where('user_id', $auth->id)
            ->orderBy('created_at', 'desc');

        // Filter Tipe
        if ($this->filterTipe) {
            $cashflowsQuery->where('tipe', $this->filterTipe);
        }
        
        // Logika untuk Search (Hanya Judul)
        if (!empty($this->search)) {
            $cashflowsQuery->where('title', 'like', '%' . $this->search . '%');
        }
        
        // Menggunakan 20 untuk pagination
        $cashflows = $cashflowsQuery->paginate(20);
        
        return view('livewire.home-livewire', [
            'totalPemasukan' => $totalPemasukan,
            'totalPengeluaran' => $totalPengeluaran,
            'totalAkumulasi' => $totalAkumulasi,
            'chartData' => $chartData,
            'cashflows' => $cashflows,
        ]);
    }

    // --- Logika Tambah Data ---
    public function initAddModal()
    {
        // Reset properti untuk memastikan form tambah bersih
        $this->reset([
            'addCashflowTitle', 
            'addCashflowNominal', 
            'addCashflowDescription', 
            // 'addCashflowFile' // <-- DIHAPUS
        ]);
        $this->addCashflowTipe = 'pemasukan'; // Set default
        
        $this->dispatch('openModal', id: 'addCashflowModal');
    }

    public function addCashflow()
    {
        $validated = $this->validate([
            'addCashflowTitle' => 'required|string|max:255',
            'addCashflowTipe' => 'required|in:pemasukan,pengeluaran',
            'addCashflowNominal' => 'required|integer|min:1',
            'addCashflowDescription' => 'nullable|string',
            // 'addCashflowFile' => 'nullable|image|max:2048', // <-- DIHAPUS
        ]);

        $cashflow = new Cashflow();
        $cashflow->user_id = Auth::id();
        $cashflow->title = $validated['addCashflowTitle'];
        $cashflow->tipe = strtolower($validated['addCashflowTipe']);
        $cashflow->nominal = $validated['addCashflowNominal'];
        $cashflow->description = $validated['addCashflowDescription'];

        // --- BLOK LOGIKA UPLOAD FILE DIHAPUS DARI SINI ---

        $cashflow->save();

        // Tutup modal
        $this->dispatch('closeModal', id: 'addCashflowModal');
        
        // Kirim notifikasi SweetAlert
        $this->dispatch('swal:alert', 
            icon: 'success', 
            title: 'Berhasil', 
            text: 'Data cashflow berhasil ditambahkan.'
        );

        // Reset form
        $this->reset([
            'addCashflowTitle', 
            'addCashflowTipe', 
            'addCashflowNominal', 
            'addCashflowDescription', 
            // 'addCashflowFile' // <-- DIHAPUS
        ]);
        $this->addCashflowTipe = 'pemasukan'; // Kembalikan ke default
    }


    // --- Logika Edit Data ---
    public function initEditModal($id)
    {
        $cashflow = Cashflow::find($id);
        
        if ($cashflow && $cashflow->user_id == Auth::id()) {
            $this->editCashflowId = $cashflow->id;
            $this->editCashflowTitle = $cashflow->title;
            $this->editCashflowTipe = strtolower($cashflow->tipe);
            $this->editCashflowNominal = $cashflow->nominal;
            $this->editCashflowDescription = $cashflow->description;

            $this->dispatch('openModal', id: 'editCashflowModal');
        }
    }

    public function editCashflow()
    {
        $validated = $this->validate([
            'editCashflowTitle' => 'required|string|max:255',
            'editCashflowTipe' => 'required|in:pemasukan,pengeluaran',
            'editCashflowNominal' => 'required|integer|min:1',
            'editCashflowDescription' => 'nullable|string',
        ]);

        $cashflow = Cashflow::find($this->editCashflowId);

        if ($cashflow && $cashflow->user_id == Auth::id()) {
            $cashflow->title = $validated['editCashflowTitle'];
            $cashflow->tipe = strtolower($validated['editCashflowTipe']);
            $cashflow->nominal = $validated['editCashflowNominal'];
            $cashflow->description = $validated['editCashflowDescription'];
            $cashflow->save();
            
            $this->dispatch('closeModal', id: 'editCashflowModal');
            
            $this->dispatch('swal:alert', 
                icon: 'success', 
                title: 'Berhasil', 
                text: 'Data cashflow berhasil diperbarui.'
            );
        }
    }


    // --- Logika Hapus Data ---
    public function initDeleteModal($id)
    {
        $cashflow = Cashflow::find($id);
        
        if ($cashflow && $cashflow->user_id == Auth::id()) {
            $this->deleteCashflowId = $cashflow->id;
            $this->deleteCashflowTitle = $cashflow->title;
            
            $this->reset(['deleteCashflowConfirmTitle']); 

            $this->dispatch('openModal', id: 'deleteCashflowModal');
        }
    }

    public function deleteCashflow()
    {
        $cashflow = Cashflow::find($this->deleteCashflowId);

        if ($cashflow && $cashflow->user_id == Auth::id()) {
            
            $this->validate([
                'deleteCashflowConfirmTitle' => 'required|in:' . $cashflow->title,
            ], [
                'deleteCashflowConfirmTitle.in' => 'Judul konfirmasi tidak cocok.',
                'deleteCashflowConfirmTitle.required' => 'Judul konfirmasi wajib diisi.'
            ]);

            if ($cashflow->cover && Storage::disk('public')->exists($cashflow->cover)) {
                Storage::disk('public')->delete($cashflow->cover);
            }

            $cashflow->delete();

            $this->dispatch('closeModal', id: 'deleteCashflowModal');
            
            $this->dispatch('swal:alert', 
                icon: 'success', 
                title: 'Berhasil', 
                text: 'Data cashflow telah dihapus.'
            );
        }
    }
}