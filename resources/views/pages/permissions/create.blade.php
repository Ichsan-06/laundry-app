@extends('layouts.app')

@section('title', 'Tambah Permission')
@section('page-title', 'Tambah Permission')
@section('page-subtitle', 'Buat permission baru lalu hubungkan ke role yang membutuhkannya.')

@section('content')
    <div class="mx-auto max-w-5xl rounded-[32px] border border-white/70 bg-white p-6 shadow-soft sm:p-8">
        <form action="{{ route('permissions.store') }}" method="POST">
            @include('pages.permissions._form', ['submitLabel' => 'Simpan Permission', 'isEdit' => false])
        </form>
    </div>
@endsection
