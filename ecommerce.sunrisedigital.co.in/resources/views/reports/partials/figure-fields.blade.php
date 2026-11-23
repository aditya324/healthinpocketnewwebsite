@php
    $fields = \App\Support\WeeklyReportFigures::fields($report);
    $groups = collect($fields)->groupBy('group');
    $index = 0;
@endphp

@if ($fields !== [])
    <section class="rounded-2xl border border-[#ebe6df] bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-[#1a1a1a]">Numbers</h2>
        <p class="mt-2 max-w-2xl text-sm text-[#6f6a64]">Change a figure here when the PDF was read incorrectly. Leave the file field empty to keep the current PDF.</p>

        <div class="mt-6 flex flex-col gap-8">
            @foreach ($groups as $group => $items)
                <div>
                    <h3 class="text-sm font-semibold text-[#1a1a1a]">{{ $group }}</h3>
                    <div class="mt-3 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($items as $field)
                            <div>
                                <label for="figure-{{ $index }}" class="text-sm font-medium text-[#1a1a1a]">{{ $field['label'] }}</label>
                                <input type="hidden" name="figures[{{ $index }}][path]" value="{{ $field['path'] }}">
                                <input id="figure-{{ $index }}" name="figures[{{ $index }}][value]" type="text" value="{{ old('figures.'.$index.'.value', $field['value']) }}" class="mt-1.5 h-11 w-full rounded-xl border border-[#e4e0da] px-3 text-sm text-[#1a1a1a] outline-none focus:border-[#f5b400]">
                            </div>
                            @php $index++; @endphp
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endif
