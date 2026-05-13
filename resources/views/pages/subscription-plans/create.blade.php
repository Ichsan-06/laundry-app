@extends('layouts.app')

@section('title', 'Tambah Subscription Plan')
@section('page-title', 'Tambah Subscription Plan')
@section('page-subtitle', 'Atur limit outlet, limit staff, dan katalog permission untuk plan baru.')

@section('content')
    <div class="mx-auto max-w-5xl rounded-[32px] border border-white/70 bg-white p-6 shadow-soft sm:p-8">
        <form action="{{ route('subscription-plans.store') }}" method="POST">
            @include('pages.subscription-plans._form', ['submitLabel' => 'Simpan Plan'])
        </form>
    </div>
@endsection
