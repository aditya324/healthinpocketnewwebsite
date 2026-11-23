@extends('reports.layout')

@section('title', $customer->name)

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[#b8860b]">Client</p>
            <h1 class="mt-1 text-3xl font-semibold tracking-tight text-[#1a1a1a]">{{ $customer->name }}</h1>
            <p class="mt-2 text-sm text-[#6f6a64]">{{ $customer->email }}</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('reports.weekly-reports.create', ['customer' => $customer, 'kind' => 'weekly']) }}" class="inline-flex h-11 items-center rounded-full bg-[#f5b400] px-5 text-sm font-semibold text-[#1a1a1a] shadow-sm">New weekly report</a>
            <a href="{{ route('reports.weekly-reports.create', ['customer' => $customer, 'kind' => 'social']) }}" class="inline-flex h-11 items-center rounded-full border border-[#e4e0da] bg-white px-5 text-sm font-semibold text-[#1a1a1a]">Social media report</a>
            <a href="{{ route('reports.weekly-reports.create', ['customer' => $customer, 'kind' => 'seo']) }}" class="inline-flex h-11 items-center rounded-full border border-[#e4e0da] bg-white px-5 text-sm font-semibold text-[#1a1a1a]">SEO report</a>
        </div>
    </div>

    <div class="mt-6 overflow-hidden rounded-2xl border border-[#ebe6df] bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-[#ebe6df] text-xs uppercase tracking-wide text-[#8a8680]">
                <tr>
                    <th class="px-4 py-3 font-medium">Period</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 font-medium">Filed by</th>
                    <th class="px-4 py-3 font-medium"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reports as $report)
                    <tr class="border-b border-[#f3eee6] last:border-0">
                        <td class="px-4 py-3">
                            <p class="font-medium text-[#1a1a1a]">{{ $report->currentPeriodLabel() }}</p>
                            <p class="text-xs text-[#8a8680]">{{ $report->kindLabel() }}@unless ($report->isSocial()) · compared with {{ $report->previousPeriodLabel() }}@endunless</p>
                        </td>
                        <td class="px-4 py-3">
                            @if ($report->isPublished())
                                <span class="inline-flex rounded-full bg-[#e8f8ee] px-3 py-1 text-xs font-semibold text-[#1f7a3a]">Published</span>
                            @else
                                <span class="inline-flex rounded-full bg-[#fff4d6] px-3 py-1 text-xs font-semibold text-[#8a6a00]">Draft</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-[#6f6a64]">{{ $report->reporter?->name ?? 'Former reporter' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap items-center justify-end gap-2">
                                <a href="{{ route('reports.weekly-reports.show', $report) }}" class="inline-flex h-9 items-center rounded-full border border-[#e4e0da] bg-white px-3 text-sm font-medium text-[#1a1a1a]">View</a>
                                <a href="{{ route('reports.weekly-reports.edit', $report) }}" class="inline-flex h-9 items-center rounded-full bg-[#f5b400] px-3 text-sm font-semibold text-[#1a1a1a]">Edit</a>
                                <form method="POST" action="{{ route('reports.weekly-reports.destroy', $report) }}" data-confirm-title="Delete this report?" data-confirm-body="{{ $customer->name }} will no longer be able to open the {{ $report->periodWord() }} of {{ $report->currentPeriodLabel() }}." data-confirm-accept="Delete" data-confirm-tone="danger">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex h-9 items-center rounded-full border border-[#e7c1c1] bg-white px-3 text-sm font-semibold text-[#9b2c2c]">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-[#6f6a64]">No reports for this client yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($deletedReports->isNotEmpty())
        <h2 class="mt-8 text-lg font-semibold text-[#1a1a1a]">Deleted reports</h2>
        <p class="mt-1 text-sm text-[#6f6a64]">Restore puts the report back for the client. Delete forever removes the PDF and the numbers, and it cannot be undone.</p>
        <div class="mt-3 overflow-hidden rounded-2xl border border-[#ebe6df] bg-white">
            <table class="w-full text-left text-sm">
                <tbody>
                    @foreach ($deletedReports as $report)
                        <tr class="border-b border-[#f3eee6] last:border-0">
                            <td class="px-4 py-3">
                                <p class="font-medium text-[#1a1a1a]">{{ $report->currentPeriodLabel() }}</p>
                                <p class="text-xs text-[#8a8680]">{{ $report->kindLabel() }}</p>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex flex-wrap items-center justify-end gap-2">
                                    <form method="POST" action="{{ route('reports.weekly-reports.restore', $report) }}">
                                        @csrf
                                        <button type="submit" class="inline-flex h-9 items-center rounded-full bg-[#f5b400] px-3 text-sm font-semibold text-[#1a1a1a]">Restore</button>
                                    </form>
                                    <form method="POST" action="{{ route('reports.weekly-reports.force', $report) }}" data-confirm-title="Delete this report forever?" data-confirm-body="The PDF and the numbers for {{ $report->currentPeriodLabel() }} will be removed. This cannot be undone." data-confirm-accept="Delete forever" data-confirm-tone="danger">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex h-9 items-center rounded-full border border-[#e7c1c1] bg-white px-3 text-sm font-semibold text-[#9b2c2c]">Delete forever</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
