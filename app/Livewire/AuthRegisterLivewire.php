<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class AuthRegisterLivewire extends Component
{
    public $name;
    public $email;
    public $password;
    public $password_confirmation;

    public function render()
    {
        return view('livewire.auth-register-livewire');
    }

    // *** PERUBAHAN DI SINI ***
    // Mengubah nama fungsi dari 'register' menjadi 'registerUser'
    // agar cocok dengan panggilan wire:submit di file Blade
    public function registerUser() 
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);
        $user->save();

        return redirect()->route('login')->with('success', 'Registrasi berhasil, silakan login.');
    }
}