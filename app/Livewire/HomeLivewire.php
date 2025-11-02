<?php

namespace App\Livewire;

use App\Models\Cashflow;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HomeLivewire extends Component
{
    use WithPagination, WithFileUploads;

    // Mengatur tema pagination agar sesuai Bootstrap
    protected $paginationTheme = 'bootstrap';

    public $auth;

    // --- Properti untuk Add Cashflow ---
    public $addCashflowTitle;
    public $addCashflowTipe = 'pemasukan';
    public $addCashflowNominal;
    public $addCashflowDescription;
    public $addCashflowFile;

    // --- Properti untuk Filter ---
    public $filterSearch = '';
    public $filterTipe = 'semua';
    public $filterBulan;
    public $filterTahun;

    // --- Properti untuk Edit/Delete ---
    public $selectedCashflow;
    public $editCashflowTitle;
    public $editCashflowTipe;
    public $editCashflowNominal;
    public $editCashflowDescription;
    public $deleteCashflowTitle;
    public $deleteCashflowConfirmTitle;
    public $editCoverCashflowFile;


    protected $queryString = [
        'filterSearch' => ['except' => '', 'as' => 'search'],
        'filterTipe' => ['except' => 'semua', 'as' => 'tipe'],
        'filterBulan' => ['except' => '', 'as' => 'bulan'],
        'filterTahun' => ['except' => '', 'as' => 'tahun'],
    ];

    public function mount()
    {
        $this->auth = Auth::user();
        if (empty($this->filterTahun)) {
            $this->filterTahun = now()->format('Y');
        }
        if (empty($this->filterBulan)) {
            $this->filterBulan = now()->format('Y-m');
        }
    }

    public function resetFilter()
    {
        $this->reset(['filterSearch', 'filterTipe']);
        $this->filterTahun = now()->format('Y');
        $this->filterBulan = now()->format('Y-m');
        $this->resetPage();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['filterSearch', 'filterTipe', 'filterBulan', 'filterTahun'])) {
            $this->resetPage();
        }
    }

    // Fungsi untuk mengambil data Chart
    public function getChartData()
    {
        $days = [];
        $pemasukanData = [];
        $pengeluaranData = [];

        // Ambil data 7 hari terakhir (termasuk hari ini)
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateString = $date->format('Y-m-d');
            $dayName = $date->format('D, j M'); // Format: Min, 2 Nov

            $days[] = $dayName;

            // Query Pemasukan pada hari itu
            $pemasukan = Cashflow::where('user_id', $this->auth->id)
                ->where('tipe', 'pemasukan')
                ->whereDate('created_at', $dateString)
                ->sum('nominal');
            $pemasukanData[] = (int) $pemasukan;

            // Query Pengeluaran pada hari itu
            $pengeluaran = Cashflow::where('user_id', $this->auth->id)
                ->where('tipe', 'pengeluaran')
                ->whereDate('created_at', $dateString)
                ->sum('nominal');
            $pengeluaranData[] = (int) $pengeluaran;
        }

        // Kembalikan data dalam format yang siap dipakai ApexCharts
        return [
            'categories' => $days,
            'series' => [
                [
                    'name' => 'Pemasukan',
                    'data' => $pemasukanData,
                ],
                [
                    'name' => 'Pengeluaran',
                    'data' => $pengeluaranData,
                ],
            ],
        ];
    }

    // Fungsi render untuk menampilkan data
    public function render()
    {
        // 1. Mulai query dasar
        $query = Cashflow::where('user_id', $this->auth->id);

        // 2. Terapkan filter non-tipe (Search dan Waktu)
        if (!empty($this->filterSearch)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->filterSearch . '%')
                  ->orWhere('description', 'like', '%' . $this->filterSearch . '%');
            });
        }

        if (!empty($this->filterBulan)) {
            $query->whereMonth('created_at', date('m', strtotime($this->filterBulan)))
                  ->whereYear('created_at', date('Y', strtotime($this->filterBulan)));
        } elseif (!empty($this->filterTahun)) {
            $query->whereYear('created_at', $this->filterTahun);
        }

        // 3. Clone query SEKARANG (sebelum filter 'tipe') untuk menghitung total
        $query_for_totals = clone $query;

        // 4. SEKARANG terapkan filter 'tipe' hanya ke query utama (untuk daftar)
        if ($this->filterTipe === 'pemasukan') {
            $query->where('tipe', 'pemasukan');
        } elseif ($this->filterTipe === 'pengeluaran') {
            $query->where('tipe', 'pengeluaran');
        }
        // Jika 'semua', $query tidak diubah.

        // 5. Hitung total menggunakan query clone (yang tidak terpengaruh filter 'tipe')
        $totalPemasukan = (clone $query_for_totals)->where('tipe', 'pemasukan')->sum('nominal');
        $totalPengeluaran = (clone $query_for_totals)->where('tipe', 'pengeluaran')->sum('nominal');
        $saldo = $totalPemasukan - $totalPengeluaran;

        // ======================================================
        // PERBAIKAN: Mengubah 10 menjadi 20
        // ======================================================
        // 6. Paginate query utama (yang memiliki semua filter)
        $cashflows = $query->orderBy('created_at', 'desc')->paginate(20);
        // ======================================================
        // END PERBAIKAN
        // ======================================================
        
        // 7. Ambil data chart
        $chartData = $this->getChartData();

        return view('livewire.home-livewire', [
            'cashflows' => $cashflows,
            'totalPemasukan' => $totalPemasukan,
            'totalPengeluaran' => $totalPengeluaran,
            'totalAkumulasi' => $saldo,
            'chartData' => $chartData, // Kirim data chart ke view
        ]);
    }

    // --- Logika Add Cashflow ---
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
            'tipe' => $validated['addCashflowTipe'],
            'nominal' => $validated['addCashflowNominal'],
            'description' => $validated['addCashflowDescription'],
            'cover' => $path,
        ]);

        $this->reset(['addCashflowTitle', 'addCashflowTipe', 'addCashflowNominal', 'addCashflowDescription', 'addCashflowFile']);
        $this->addCashflowTipe = 'pemasukan';
        $this->dispatch('closeModal', id: 'addCashflowModal');
        
        $this->dispatch('swal:alert', 
            icon: 'success', 
            title: 'Berhasil', 
            text: 'Cashflow baru berhasil ditambahkan.'
        );

        $this->resetPage();

        // Update chart
        $this->dispatch('updateChart', data: $this->getChartData());
    }


    // --- Logika Modal Edit, Delete, Cover ---

    private function loadCashflow($id)
    {
        $this->selectedCashflow = Cashflow::where('id', $id)
                                         ->where('user_id', $this->auth->id)
                                         ->firstOrFail();
    }

    public function initEditModal($id)
    {
        $this->loadCashflow($id); 
        
        $this->editCashflowTitle = $this->selectedCashflow->title;
        $this->editCashflowTipe = strtolower($this->selectedCashflow->tipe);
        $this->editCashflowNominal = $this->selectedCashflow->nominal;
        $this->editCashflowDescription = $this->selectedCashflow->description;

        $this->reset(['deleteCashflowConfirmTitle', 'editCoverCashflowFile']);
        $this->dispatch('openModal', id: 'editCashflowModal');
    }

    public function editCashflow()
    {
        if (!$this->selectedCashflow) return; 

        $validated = $this->validate([
            'editCashflowTitle' => 'required|string|max:255',
            'editCashflowTipe' => 'required|in:pemasukan,pengeluaran',
            'editCashflowNominal' => 'required|integer|min:1',
            'editCashflowDescription' => 'nullable|string',
        ]);

        $this->selectedCashflow->title = $validated['editCashflowTitle'];
        $this->selectedCashflow->tipe = strtolower($validated['editCashflowTipe']);
        $this->selectedCashflow->nominal = $validated['editCashflowNominal'];
        $this->selectedCashflow->description = $validated['editCashflowDescription'];
        $this->selectedCashflow->save();

        $this->dispatch('closeModal', id: 'editCashflowModal');
        $this->dispatch('swal:alert', 
            icon: 'success', 
            title: 'Berhasil', 
            text: 'Data cashflow berhasil diperbarui.'
        );
        
        $this->reset(['selectedCashflow', 'editCashflowTitle', 'editCashflowTipe', 'editCashflowNominal', 'editCashflowDescription']);

        // Update chart
        $this->dispatch('updateChart', data: $this->getChartData());
    }

    public function initDeleteModal($id)
    {
        $this->loadCashflow($id);
        
        $this->deleteCashflowTitle = $this->selectedCashflow->title;
        $this->reset(['deleteCashflowConfirmTitle', 'editCoverCashflowFile']);
        $this->dispatch('openModal', id: 'deleteCashflowModal');
    }

    public function deleteCashflow()
    {
        if (!$this->selectedCashflow) return; 

        $this->validate([
            'deleteCashflowConfirmTitle' => 'required|in:' . $this->selectedCashflow->title,
        ], [
            'deleteCashflowConfirmTitle.in' => 'Judul konfirmasi tidak cocok.',
            'deleteCashflowConfirmTitle.required' => 'Judul konfirmasi wajib diisi.'
        ]);

        if ($this->selectedCashflow->cover && Storage::disk('public')->exists($this->selectedCashflow->cover)) {
            Storage::disk('public')->delete($this->selectedCashflow->cover);
        }

        $this->selectedCashflow->delete();

        $this->dispatch('closeModal', id: 'deleteCashflowModal');
        $this->dispatch('swal:alert', 
            icon: 'success', 
            title: 'Berhasil', 
            text: 'Cashflow berhasil dihapus.'
        );

        $this->reset(['selectedCashflow', 'deleteCashflowTitle', 'deleteCashflowConfirmTitle']);
        $this->resetPage(); 

        // Update chart
        $this->dispatch('updateChart', data: $this->getChartData());
    }

    public function initEditCoverModal($id)
    {
        $this->loadCashflow($id);
        $this->reset(['editCoverCashflowFile']);
        $this->dispatch('openModal', id: 'editCoverCashflowModal');
    }
    
    public function editCoverCashflow()
    {
        if (!$this->selectedCashflow) return; 

        $this->validate([
            'editCoverCashflowFile' => 'required|image|max:2048',
        ]);

        if ($this->editCoverCashflowFile) {
            
            if ($this->selectedCashflow->cover && Storage::disk('public')->exists($this->selectedCashflow->cover)) {
                Storage::disk('public')->delete($this->selectedCashflow->cover);
            }

            $userId = $this->auth->id;
            $dateNumber = now()->format('YmdHis');
            $extension = $this->editCoverCashflowFile->getClientOriginalExtension();
            $filename = $userId . '_' . $dateNumber . '.' . $extension;
            $path = $this->editCoverCashflowFile->storeAs('covers', $filename, 'public');
            $this->selectedCashflow->cover = $path;
            $this->selectedCashflow->save();
        }

        $this->reset(['editCoverCashflowFile', 'selectedCashflow']);
        $this->dispatch('closeModal', id: 'editCoverCashflowModal');
        $this->dispatch('swal:alert', 
            icon: 'success', 
            title: 'Berhasil', 
            text: 'Cover cashflow berhasil diperbarui.'
        );
    }
}