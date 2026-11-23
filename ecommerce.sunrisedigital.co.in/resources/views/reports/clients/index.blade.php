@extends('reports.layout')

@section('title', 'Clients')

@section('content')
    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[#b8860b]">Reports</p>
    <h1 class="mt-1 text-3xl font-semibold tracking-tight text-[#1a1a1a]">Clients</h1>
    <p class="mt-2 text-sm text-[#6f6a64]">Choose a client, then file that week’s performance report. You see clients assigned to you, and clients who are not assigned yet.</p>

    <section class="mt-6 rounded-2xl border border-[#ebe6df] bg-white p-5">
        <h2 class="text-lg font-semibold text-[#1a1a1a]">Still to file</h2>
        <div class="mt-4 grid gap-4 md:grid-cols-3">
            @foreach (['weekly' => 'This week', 'social' => 'Social media this month', 'seo' => 'SEO this month'] as $key => $heading)
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[#8a8680]">{{ $heading }}</p>
                    @if ($missing[$key]->isEmpty())
                        <p class="mt-2 text-sm text-[#6f6a64]">Everyone is filed.</p>
                    @else
                        <ul class="mt-2 flex flex-col gap-1 text-sm">
                            @foreach ($missing[$key] as $client)
                                <li><a href="{{ route('reports.clients.show', $client) }}" class="font-medium text-[#1a1a1a] underline">{{ $client->name }}</a></li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endforeach
        </div>
    </section>

    @include('admin.partials.filters', [
        'action' => route('reports.home'),
        'values' => $filters,
        'filtering' => $filtering,
        'count' => $clients->count(),
        'noun' => 'client',
        'fields' => [
            ['name' => 'q', 'type' => 'search', 'label' => 'Search', 'placeholder' => 'Name or email'],
        ],
    ])

    <div class="mt-6 overflow-hidden rounded-2xl border border-[#ebe6df] bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-[#ebe6df] text-xs uppercase tracking-wide text-[#8a8680]">
                <tr>
                    <th class="px-4 py-3 font-medium">Client</th>
                    <th class="px-4 py-3 font-medium">Reports</th>
                    <th class="px-4 py-3 font-medium"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($clients as $client)
                    <tr class="border-b border-[#f3eee6] last:border-0">
                        <td class="px-4 py-3">
                            <p class="font-medium text-[#1a1a1a]">{{ $client->name }}</p>
                            <p class="text-xs text-[#8a8680]">{{ $client->email }}</p>
                        </td>
                        <td class="px-4 py-3">{{ $client->weekly_reports_count }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('reports.clients.show', $client) }}" class="font-medium text-[#1a1a1a] underline">Open</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-8 text-[#6f6a64]">{{ $filtering ? 'No clients match this search.' : 'No clients yet.' }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
