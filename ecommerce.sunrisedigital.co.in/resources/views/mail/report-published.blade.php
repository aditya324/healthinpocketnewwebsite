<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Your report is ready</title>
</head>
<body style="margin:0;background:#f7f4ef;color:#1a1a1a;font-family:Arial,sans-serif;">
    <div style="max-width:560px;margin:0 auto;padding:32px 20px;">
        <p style="margin:0;font-size:12px;letter-spacing:0.14em;text-transform:uppercase;color:#b8860b;">Sunrise Digital</p>
        <h1 style="margin:8px 0 0;font-size:24px;">Your {{ strtolower($report->kindLabel()) }} report is ready</h1>
        <p style="margin:16px 0 0;font-size:15px;line-height:1.6;">Hello {{ $report->client?->name }},</p>
        <p style="margin:8px 0 0;font-size:15px;line-height:1.6;">The report for {{ $report->currentPeriodLabel() }} is on your dashboard. You can compare it with your other {{ $report->periodWord() }}s from the same page.</p>
        <p style="margin:24px 0 0;"><a href="{{ route('dashboard.reports.show', $report) }}" style="color:#1a1a1a;">Open {{ $report->currentPeriodLabel() }}</a></p>
    </div>
</body>
</html>
