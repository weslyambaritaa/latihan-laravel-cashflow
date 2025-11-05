<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AuthLoginLivewire extends Component
{
    public $email;
    public $password;

    public function render()
    {
        return view('livewire.auth-login-livewire');
    }

    // *** PERUBAHAN DI SINI ***
    // Nama fungsi diubah dari 'login' menjadi 'loginUser'
    // agar cocok dengan file blade (form) yang baru
    public function loginUser()
    {
        $validated = $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($validated)) {
            return redirect()->route('app.home');
        }

        $this->addError('email', 'Email atau password salah.');
    }
}