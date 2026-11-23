@extends('reports.layout')

@section('title', $report->exists ? 'Edit '.$report->kind.' report' : 'New '.$report->kind.' report')

@section('content')
    @php
        $phrase = match ($report->kind) {
            'social' => 'social media',
            'seo' => 'SEO',
            default => 'weekly',
        };
        $hint = match ($report->kind) {
            'social' => 'Use the monthly social media report you send the client. It should name the month and include Facebook, Instagram, YouTube, or Google Business Profile.',
            'seo' => 'Use the monthly SEO report you send the client. It should name the month and compare it with the previous month.',
            default => 'Use the same weekly report you send the client, with the screenshots and the written summary. It needs a current week and a previous week.',
        };
        $input = 'mt-1.5 block w-full text-sm text-[#1a1a1a] file:mr-4 file:rounded-full file:border-0 file:bg-[#111111] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white';
    @endphp

    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[#b8860b]">{{ $customer->name }}</p>
    <h1 class="mt-1 text-3xl font-semibold tracking-tight text-[#1a1a1a]">{{ $report->exists ? 'Edit '.$phrase.' report' : 'New '.$phrase.' report' }}</h1>
    <p class="mt-2 max-w-2xl text-sm text-[#6f6a64]">{{ $report->exists ? 'Correct a number that was read wrong. Upload a new PDF only when you want to replace the whole report. The client still opens the original PDF.' : 'Upload the '.$phrase.' PDF. The numbers are read from the file, and the client can open that same PDF from their dashboard.' }}</p>

    @if ($errors->any())
        <div class="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <p class="font-semibold">This report was not saved.</p>
            <ul class="mt-2 list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ $report->exists ? route('reports.weekly-reports.update', $report) : route('reports.weekly-reports.store', $customer) }}" enctype="multipart/form-data" class="mt-6 flex flex-col gap-6">
        @csrf
        @if ($report->exists)
            @method('PUT')
        @endif
        <input type="hidden" name="kind" value="{{ $report->kind ?: 'weekly' }}">

        @if ($report->exists)
            @include('reports.partials.figure-fields')
        @endif

        <section class="max-w-2xl rounded-2xl border border-[#ebe6df] bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-[#1a1a1a]">{{ ucfirst($phrase) }} report PDF</h2>
            @if ($report->exists && filled($report->pdf_original_name))
                <p class="mt-2 text-sm text-[#6f6a64]">Current file: {{ $report->pdf_original_name }}. Upload a new PDF only if you want to replace it.</p>
            @else
                <p class="mt-2 text-sm text-[#6f6a64]">{{ $hint }} PDF only, up to 20 MB.</p>
            @endif
            <div class="mt-4">
                <label for="pdf" class="text-sm font-medium text-[#1a1a1a]">{{ $report->exists ? 'Replacement PDF' : 'PDF' }}</label>
                <input id="pdf" name="pdf" type="file" accept="application/pdf,.pdf" @unless ($report->exists && filled($report->pdf_path)) required @endunless class="{{ $input }}">
            </div>
        </section>

        <div class="flex flex-wrap items-center gap-3">
            <button type="submit" name="intent" value="draft" class="inline-flex h-11 items-center rounded-full border border-[#e4e0da] bg-white px-5 text-sm font-semibold text-[#1a1a1a]">Save draft</button>
            <button type="submit" name="intent" value="publish" class="inline-flex h-11 items-center rounded-full bg-[#f5b400] px-5 text-sm font-semibold text-[#1a1a1a]">Publish</button>
            <a href="{{ route('reports.clients.show', $customer) }}" class="text-sm font-medium text-[#6f6a64] underline">Cancel</a>
        </div>
    </form>
@endsection
