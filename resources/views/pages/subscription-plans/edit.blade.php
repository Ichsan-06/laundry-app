@extends('layouts.app')

@section('title', 'Edit Subscription Plan')
@section('page-title', 'Edit Subscription Plan')
@section('page-subtitle', 'Perbarui permission plan, limit outlet, dan limit staff sesuai kebutuhan bisnis.')

@section('content')
    <div class="mx-auto max-w-5xl rounded-[32px] border border-white/70 bg-white p-6 shadow-soft sm:p-8">
        <form action="{{ route('subscription-plans.update', $plan) }}" method="POST">
            @include('pages.subscription-plans._form', ['submitLabel' => 'Update Plan', 'isEdit' => true])
        </form>
    </div>
@endsection
