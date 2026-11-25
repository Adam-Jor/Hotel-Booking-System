@extends('layouts.admin')

@section('title', 'Payments')
@section('subtitle', 'Verify transfer proofs and track revenue')

@section('content')
    <livewire:admin.payment-table />
@endsection
