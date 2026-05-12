@extends('layouts.app')

@section('title', 'Tambah Role')
@section('page-title', 'Tambah Role')
@section('page-subtitle', 'Buat role baru dan pilih permission yang melekat pada role tersebut.')

@section('content')
    <div class="mx-auto max-w-5xl rounded-[32px] border border-white/70 bg-white p-6 shadow-soft sm:p-8">
        <form action="{{ route('roles.store') }}" method="POST">
            @include('pages.roles._form', ['submitLabel' => 'Simpan Role', 'isEdit' => false])
        </form>
    </div>
@endsection
