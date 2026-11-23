@extends('dashboard.layout')

@section('title', $report->title)

@section('content')
    <a href="{{ route('dashboard.reports.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-[#1a1a1a] underline">Your reports</a>
    @if (session('status'))
        <p class="mt-4 rounded-xl border border-[#ead89a] bg-[#fff8e6] px-4 py-3 text-sm font-medium text-[#1a1a1a]">{{ session('status') }}</p>
    @endif
    <div class="mt-4 flex flex-wrap items-center gap-3">
        <a href="{{ route('weekly-reports.summary', $report) }}" class="inline-flex h-11 items-center rounded-full border border-[#e4e0da] bg-white px-5 text-sm font-semibold text-[#1a1a1a]">Download this page</a>
    </div>
    <div class="mt-4">
        @include('reports.partials.document')
    </div>
    @include('reports.partials.flag-form')
@endsection
