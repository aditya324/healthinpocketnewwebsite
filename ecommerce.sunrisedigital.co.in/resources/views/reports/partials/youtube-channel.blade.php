@php
    use App\Support\WeeklyReportFields;
@endphp

<div data-channel class="rounded-xl border border-[#ebe6df] p-4">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0 flex-1">
            <label class="text-sm font-medium text-[#1a1a1a]" for="youtube-{{ $index }}-name">Channel name</label>
            <input id="youtube-{{ $index }}-name" name="youtube[{{ $index }}][name]" value="{{ data_get($channel, 'name') }}" placeholder="BG Road" class="mt-1 w-full rounded-xl border border-[#e4e0da] bg-white px-3 py-2 text-sm outline-none focus:border-[#f5b400]">
            @error('youtube.'.$index.'.name')
                <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
            @enderror
        </div>
        <button type="button" data-remove-channel class="mt-7 shrink-0 text-sm font-medium text-[#9b2c2c]">Remove</button>
    </div>

    <div class="mt-4 grid gap-6 lg:grid-cols-2">
        @include('reports.partials.metrics', [
            'heading' => 'Current week',
            'name' => 'youtube['.$index.'][current]',
            'id' => 'youtube-'.$index.'-current',
            'fields' => WeeklyReportFields::youtubeMetrics(),
            'values' => data_get($channel, 'current', []),
        ])
        @include('reports.partials.metrics', [
            'heading' => 'Previous week',
            'name' => 'youtube['.$index.'][previous]',
            'id' => 'youtube-'.$index.'-previous',
            'fields' => WeeklyReportFields::youtubeMetrics(),
            'values' => data_get($channel, 'previous', []),
        ])
    </div>

    <div class="mt-4">
        <label class="text-sm font-medium text-[#1a1a1a]" for="youtube-{{ $index }}-shots">Screenshots for this channel</label>
        <input id="youtube-{{ $index }}-shots" name="youtube[{{ $index }}][screenshots][]" type="file" accept="image/jpeg,image/png,image/webp" multiple class="mt-1 block w-full text-sm text-[#6f6a64]">
    </div>
</div>
