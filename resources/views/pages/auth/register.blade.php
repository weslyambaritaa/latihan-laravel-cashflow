@extends('layouts.auth')

{{-- Set Judul Halaman --}}
@section('title', 'Register - Cashflow App')

@section('content')

    {{-- Panggil Komponen Livewire Register --}}
    @livewire('auth-register-livewire')

@endsection