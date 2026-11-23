@php
    $rows = [];

    foreach ($fields as $key => $label) {
        $value = data_get($values, $key);

        if (! filled($value)) {
            continue;
        }

        $rows[] = [
            'label' => $label,
            'value' => $value,
            'change' => ! empty($changes) ? data_get($values, $key.'_change') : null,
        ];
    }
@endphp

@if ($rows !== [])
    <div>
        @if (! empty($heading))
            <h4 class="text-sm font-semibold text-[#1a1a1a]">{{ $heading }}</h4>
        @endif
        <ul class="{{ empty($heading) ? '' : 'mt-2' }} flex flex-col gap-1 text-sm leading-6 text-[#1a1a1a]">
            @foreach ($rows as $row)
                <li>
                    <span class="font-medium">{{ $row['label'] }}:</span>
                    {{ $row['value'] }}
                    @if (filled($row['change']))
                        ({{ trim((string) $row['change'], "() \t") }})
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
@endif
