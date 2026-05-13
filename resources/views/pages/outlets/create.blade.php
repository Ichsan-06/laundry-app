@extends('layouts.app')

@section('title', 'Tambah Outlet')
@section('page-title', 'Tambah Outlet')
@section('page-subtitle', 'Tambahkan outlet baru untuk tenant Anda sesuai limit plan.')

@section('content')
    <div class="mx-auto max-w-3xl rounded-[32px] border border-white/70 bg-white p-6 shadow-soft sm:p-8">
        <form action="{{ route('outlets.store') }}" method="POST">
            @include('pages.outlets._form', ['submitLabel' => 'Simpan Outlet'])
        </form>
    </div>
@endsection
