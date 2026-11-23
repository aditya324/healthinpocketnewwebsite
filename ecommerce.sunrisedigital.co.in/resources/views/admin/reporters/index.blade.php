@extends('admin.layout')

@section('title', 'Reporters')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[#b8860b]">Reports</p>
            <h1 class="mt-1 text-3xl font-semibold tracking-tight text-[#1a1a1a]">Reporters</h1>
            <p class="mt-2 text-sm text-[#6f6a64]">These people log in on the reports dashboard. They do not see the store admin.</p>
        </div>
        <a href="{{ route('admin.reporters.create') }}" class="inline-flex h-11 items-center rounded-full bg-[#f5b400] px-5 text-sm font-semibold text-[#1a1a1a] shadow-sm">New reporter</a>
    </div>

    <div class="mt-6 overflow-hidden rounded-2xl border border-[#ebe6df] bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-[#ebe6df] text-xs uppercase tracking-wide text-[#8a8680]">
                <tr>
                    <th class="px-4 py-3 font-medium">Reporter</th>
                    <th class="px-4 py-3 font-medium">Reports</th>
                    <th class="px-4 py-3 font-medium"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reporters as $reporter)
                    <tr class="border-b border-[#f3eee6] last:border-0">
                        <td class="px-4 py-3">
                            <p class="font-medium text-[#1a1a1a]">{{ $reporter->name }}</p>
                            <p class="text-xs text-[#8a8680]">{{ $reporter->email }}</p>
                        </td>
                        <td class="px-4 py-3">{{ $reporter->weekly_reports_count }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.reporters.edit', $reporter) }}" class="font-medium text-[#1a1a1a] underline">Edit</a>
                                <form method="POST" action="{{ route('admin.reporters.destroy', $reporter) }}" data-confirm-title="Remove {{ $reporter->name }}?" data-confirm-body="They will not be able to log in. Reports already filed stay with the client." data-confirm-accept="Remove" data-confirm-tone="danger">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-medium text-[#9b2c2c]">Remove</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-8 text-[#6f6a64]">No reporters yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
