@extends('layouts.app')
@section('content')
        @livewire('cashflow-livewire', ['cashflow' => $cashflow])
@endsection
