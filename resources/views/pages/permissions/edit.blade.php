@extends('layouts.app')

@section('title', 'Edit Permission')
@section('page-title', 'Edit Permission')
@section('page-subtitle', 'Perbarui permission dan atur role yang memilikinya.')

@section('content')
    <div class="mx-auto max-w-5xl rounded-[32px] border border-white/70 bg-white p-6 shadow-soft sm:p-8">
        <form action="{{ route('permissions.update', $permission) }}" method="POST">
            @include('pages.permissions._form', ['submitLabel' => 'Update Permission', 'isEdit' => true])
        </form>
    </div>
@endsection
