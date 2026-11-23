<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $report->title }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1a1a1a; font-size: 12px; }
        h1 { font-size: 20px; margin: 0 0 6px; }
        h2 { font-size: 14px; margin: 18px 0 6px; }
        p { margin: 0 0 8px; color: #4b4742; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 6px 8px; border-bottom: 1px solid #ebe6df; }
        th { font-size: 11px; color: #8a8680; }
    </style>
</head>
<body>
    <p style="color:#b8860b;font-size:11px;letter-spacing:0.12em;text-transform:uppercase;">{{ $report->kindLabel() }} report</p>
    <h1>{{ $report->title }}</h1>
    <p>{{ $report->currentPeriodLabel() }}@unless ($report->isSocial()) · compared with {{ $report->previousPeriodLabel() }}@endunless</p>
    @if ($highlights['rise'] || $highlights['drop'])
        <p>
            @if ($highlights['rise']){{ $highlights['rise']['label'] }} is up {{ $highlights['rise']['change'] }}. @endif
            @if ($highlights['drop']){{ $highlights['drop']['label'] }} is down {{ ltrim($highlights['drop']['change'], '-') }}. @endif
        </p>
    @endif
    @foreach ($groups as $group => $items)
        <h2>{{ $group }}</h2>
        <table>
            <thead>
                <tr>
                    <th>Figure</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $field)
                    <tr>
                        <td>{{ $field['label'] }}</td>
                        <td>{{ $field['value'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
    <p style="margin-top:18px;">This is a copy of the figures on the dashboard. The original report PDF is separate.</p>
</body>
</html>
