@if ($report->hasFilled($notes ?? []))
    <ul class="mt-4 list-disc space-y-1 pl-5 text-sm leading-6 text-[#1a1a1a]">
        @foreach ($notes as $note)
            @if (filled($note))
                <li>{{ $note }}</li>
            @endif
        @endforeach
    </ul>
@endif
