@extends('layouts.app')

@section('title', 'Edit Role')
@section('page-title', 'Edit Role')
@section('page-subtitle', 'Perbarui nama role dan assignment permission sesuai kebutuhan akses.')

@section('content')
    <div class="mx-auto max-w-5xl rounded-[32px] border border-white/70 bg-white p-6 shadow-soft sm:p-8">
        <form action="{{ route('roles.update', $role) }}" method="POST">
            @include('pages.roles._form', ['submitLabel' => 'Update Role', 'isEdit' => true])
        </form>
    </div>
@endsection
