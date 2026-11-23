@extends('reports.layout')

@section('title', $report->title)

@section('content')
    <a href="{{ $report->client ? route('reports.clients.show', $report->client) : route('reports.home') }}" class="text-sm font-medium text-[#6f6a64] underline">Back to {{ $report->client?->name ?? 'clients' }}</a>

    <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-3">
            @if ($report->isPublished())
                <span class="inline-flex rounded-full bg-[#e8f8ee] px-3 py-1 text-xs font-semibold text-[#1f7a3a]">Published</span>
                <p class="text-sm text-[#6f6a64]">The client can open this report.</p>
            @else
                <span class="inline-flex rounded-full bg-[#fff4d6] px-3 py-1 text-xs font-semibold text-[#8a6a00]">Draft</span>
                <p class="text-sm text-[#6f6a64]">The client cannot see this report until it is published.</p>
            @endif
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('reports.weekly-reports.edit', $report) }}" class="inline-flex h-11 items-center rounded-full bg-[#f5b400] px-5 text-sm font-semibold text-[#1a1a1a]">Edit</a>
            <form method="POST" action="{{ route('reports.weekly-reports.destroy', $report) }}" data-confirm-title="Delete this report?" data-confirm-body="{{ $report->client?->name ?? 'The client' }} will no longer be able to open the {{ $report->periodWord() }} of {{ $report->currentPeriodLabel() }}." data-confirm-accept="Delete" data-confirm-tone="danger">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex h-11 items-center rounded-full border border-[#e7c1c1] bg-white px-5 text-sm font-semibold text-[#9b2c2c]">Delete</button>
            </form>
        </div>
    </div>

    <div class="mt-4 flex flex-wrap items-center gap-3">
        <a href="{{ route('weekly-reports.summary', $report) }}" class="inline-flex h-11 items-center rounded-full border border-[#e4e0da] bg-white px-5 text-sm font-semibold text-[#1a1a1a]">Download this page</a>
    </div>

    <form method="POST" action="{{ route('reports.weekly-reports.note', $report) }}" class="mt-6 max-w-2xl rounded-2xl border border-[#ebe6df] bg-white p-5">
        @csrf
        @method('PUT')
        <h2 class="text-lg font-semibold text-[#1a1a1a]">Private note</h2>
        <p class="mt-1 text-sm text-[#6f6a64]">Only reporters see this. The client does not.</p>
        <textarea name="staff_note" rows="3" class="mt-3 w-full rounded-xl border border-[#e4e0da] px-3 py-2 text-sm outline-none focus:border-[#f5b400]">{{ old('staff_note', $report->staff_note) }}</textarea>
        <button type="submit" class="mt-3 inline-flex h-10 items-center rounded-full bg-[#111111] px-4 text-sm font-semibold text-white">Save note</button>
    </form>

    @if ($report->flags->isNotEmpty())
        <section class="mt-6 max-w-2xl rounded-2xl border border-[#ead89a] bg-[#fff8e6] p-5">
            <h2 class="text-lg font-semibold text-[#1a1a1a]">Notes from the client</h2>
            <ul class="mt-3 flex flex-col gap-3">
                @foreach ($report->flags as $flag)
                    <li class="text-sm leading-6 text-[#1a1a1a]">
                        <span class="font-semibold">{{ $flag->label }}</span>
                        @if ($flag->figure)
                            <span class="text-[#6f6a64]">({{ $flag->figure }})</span>
                        @endif
                        <span class="block">{{ $flag->message }}</span>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    @if ($report->revisions->isNotEmpty())
        <section class="mt-6 max-w-2xl">
            <h2 class="text-lg font-semibold text-[#1a1a1a]">Number changes</h2>
            <div class="mt-3 overflow-hidden rounded-2xl border border-[#ebe6df] bg-white">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-[#ebe6df] text-xs uppercase tracking-wide text-[#8a8680]">
                        <tr>
                            <th class="px-4 py-3 font-medium">Figure</th>
                            <th class="px-4 py-3 font-medium">Was</th>
                            <th class="px-4 py-3 font-medium">Now</th>
                            <th class="px-4 py-3 font-medium">By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($report->revisions as $revision)
                            <tr class="border-b border-[#f3eee6] last:border-0">
                                <td class="px-4 py-3 text-[#1a1a1a]">{{ $revision->label }}</td>
                                <td class="px-4 py-3 text-[#6f6a64]">{{ $revision->old_value }}</td>
                                <td class="px-4 py-3 font-medium text-[#1a1a1a]">{{ $revision->new_value }}</td>
                                <td class="px-4 py-3 text-[#6f6a64]">{{ $revision->reporter?->name ?? 'Former reporter' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    <div class="mt-6">
        @include('reports.partials.document')
    </div>
@endsection
