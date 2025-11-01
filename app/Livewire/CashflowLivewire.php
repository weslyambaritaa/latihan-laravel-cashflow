<?php

namespace App\Livewire;

use App\Models\Cashflow;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class CashflowDetailLivewire extends Component
{
    use WithFileUploads;

    public $cashflow;
    public $auth;

    public function mount()
    {
        $this->auth = Auth::user();

        $cashflow_id = request()->route('cashflow_id');
        $targetCashflow = Cashflow::where('id', $cashflow_id)->first();
        if (!$targetCashflow) {
            return redirect()->route('app.home');
        }

        $this->cashflow = $targetCashflow;
    }

    public function render()
    {
        return view('livewire.cashflow-detail-livewire');
    }

    // Ubah Cover Cashflow
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