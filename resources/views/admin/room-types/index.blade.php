@extends('layouts.admin')

@section('title', 'Room types')
@section('subtitle', 'Categories that rooms belong to')

@section('content')
    <livewire:admin.room-type-table />
@endsection
