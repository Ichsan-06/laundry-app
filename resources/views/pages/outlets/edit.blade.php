@extends('layouts.app')

@section('title', 'Edit Outlet')
@section('page-title', 'Edit Outlet')
@section('page-subtitle', 'Perbarui informasi outlet dan status operasionalnya.')

@section('content')
    <div class="mx-auto max-w-3xl rounded-[32px] border border-white/70 bg-white p-6 shadow-soft sm:p-8">
        <form action="{{ route('outlets.update', $outlet) }}" method="POST">
            @include('pages.outlets._form', ['submitLabel' => 'Update Outlet', 'isEdit' => true])
        </form>
    </div>
@endsection
