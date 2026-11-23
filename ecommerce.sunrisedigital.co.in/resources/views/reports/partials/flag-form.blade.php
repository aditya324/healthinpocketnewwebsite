@php
    $figures = \App\Support\WeeklyReportFigures::fields($report);
@endphp
@if ($figures !== [])
    <form method="POST" action="{{ route('dashboard.reports.flags.store', $report) }}" class="mt-6 max-w-2xl rounded-2xl border border-[#ebe6df] bg-white p-5">
        @csrf
        <h2 class="text-lg font-semibold text-[#1a1a1a]">Something look wrong?</h2>
        <p class="mt-1 text-sm text-[#6f6a64]">Pick the number and send a short note. Sunrise will see it on this report.</p>
        <label for="flag-path" class="mt-4 block text-sm font-medium text-[#1a1a1a]">Number</label>
        <select id="flag-path" name="path" class="mt-1.5 h-11 w-full rounded-xl border border-[#e4e0da] bg-white px-3 text-sm">
            @foreach ($figures as $field)
                <option value="{{ $field['path'] }}" @selected(old('path') === $field['path'])>{{ $field['label'] }} · {{ $field['value'] }}</option>
            @endforeach
        </select>
        <label for="flag-message" class="mt-4 block text-sm font-medium text-[#1a1a1a]">Note</label>
        <textarea id="flag-message" name="message" rows="3" required class="mt-1.5 w-full rounded-xl border border-[#e4e0da] px-3 py-2 text-sm outline-none focus:border-[#f5b400]">{{ old('message') }}</textarea>
        @error('message')
            <p class="mt-1 text-sm text-[#9b2c2c]">{{ $message }}</p>
        @enderror
        @error('path')
            <p class="mt-1 text-sm text-[#9b2c2c]">{{ $message }}</p>
        @enderror
        <button type="submit" class="mt-4 inline-flex h-11 items-center rounded-full bg-[#111111] px-5 text-sm font-semibold text-white">Send note</button>
    </form>
@endif
