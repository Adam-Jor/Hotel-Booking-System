@extends('layouts.admin')

@section('title', 'Rooms')
@section('subtitle', 'Individual rooms, prices and availability status')

@section('content')
    <livewire:admin.room-table />
@endsection
