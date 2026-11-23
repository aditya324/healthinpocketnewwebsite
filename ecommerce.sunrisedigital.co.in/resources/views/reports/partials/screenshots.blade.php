<div>
    @if ($shots->isNotEmpty())
        <ul class="grid gap-3 sm:grid-cols-2">
            @foreach ($shots as $shot)
                <li class="rounded-xl border border-[#ebe6df] bg-[#fbfaf8] p-3">
                    <img src="{{ route('weekly-reports.screenshots.show', $shot) }}" alt="{{ ucfirst($shot->platform) }} screenshot" class="max-h-56 w-full rounded-lg bg-[#f6f3ee] object-contain">
                    @if (filled($shot->channel_name))
                        <p class="mt-2 text-xs text-[#6f6a64]">{{ $shot->channel_name }}</p>
                    @endif
                    @if (! empty($removable))
                        <label class="mt-2 flex items-center gap-2 text-sm text-[#1a1a1a]">
                            <input type="checkbox" name="remove_screenshots[]" value="{{ $shot->uuid }}" class="rounded border-[#e4e0da]">
                            Remove
                        </label>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif

    @if (! empty($input))
        <label class="mt-3 block text-sm font-medium text-[#1a1a1a]" for="{{ $id }}">{{ $label ?? 'Add screenshots' }}</label>
        <input id="{{ $id }}" name="{{ $input }}" type="file" accept="image/jpeg,image/png,image/webp" multiple class="mt-1 block w-full text-sm text-[#6f6a64]">
        <p class="mt-1 text-xs text-[#8a8680]">JPG, PNG, or WebP. Up to 8 images, 5 MB each.</p>
    @endif
</div>
