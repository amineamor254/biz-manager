@extends('layouts.app')
@section('content')
<div class="mx-auto max-w-5xl space-y-6">
    <div>
        <p class="text-sm font-semibold text-blue-700 dark:text-blue-400">Sales</p>
        <h1 class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">Create order</h1>
    </div>
    @include('orders._form', ['editing' => false])
</div>
@endsection