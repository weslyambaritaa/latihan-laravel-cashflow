@extends('layouts.auth')

{{-- Set Judul Halaman --}}
@section('title', 'Login - Cashflow App')

@section('content')

    {{-- Panggil Komponen Livewire Login --}}
    @livewire('auth-login-livewire')

@endsection