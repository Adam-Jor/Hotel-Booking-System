@extends('layouts.admin')

@section('title', 'Users')
@section('subtitle', 'Admins and customer accounts')

@section('content')
    <livewire:admin.user-table />
@endsection
