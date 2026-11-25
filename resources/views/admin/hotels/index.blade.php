@extends('layouts.admin')

@section('title', 'Hotels')
@section('subtitle', 'Manage hotel records, photos and status')

@section('content')
    <livewire:admin.hotel-table />
@endsection
