<div>
    <h3 class="text-sm font-semibold text-[#1a1a1a]">{{ $heading }}</h3>
    @if (! empty($hint))
        <p class="mt-1 text-xs text-[#8a8680]">{{ $hint }}</p>
    @endif
    <div class="mt-3 grid gap-3 sm:grid-cols-2">
        @foreach ($fields as $key => $label)
            <div @class(['sm:col-span-2 grid gap-3 sm:grid-cols-[minmax(0,1fr)_9rem]' => ! empty($changes)])>
                <div>
                    <label class="text-xs font-medium text-[#6f6a64]" for="{{ $id }}-{{ $key }}">{{ $label }}</label>
                    <input id="{{ $id }}-{{ $key }}" name="{{ $name }}[{{ $key }}]" value="{{ data_get($values, $key) }}" class="mt-1 w-full rounded-xl border border-[#e4e0da] bg-white px-3 py-2 text-sm outline-none focus:border-[#f5b400]">
                </div>
                @if (! empty($changes))
                    <div>
                        <label class="text-xs font-medium text-[#6f6a64]" for="{{ $id }}-{{ $key }}-change">Change</label>
                        <input id="{{ $id }}-{{ $key }}-change" name="{{ $name }}[{{ $key }}_change]" value="{{ data_get($values, $key.'_change') }}" placeholder="+5.3%" class="mt-1 w-full rounded-xl border border-[#e4e0da] bg-white px-3 py-2 text-sm outline-none focus:border-[#f5b400]">
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
