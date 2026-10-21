<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AdeMNEA hive report</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; color: #212529; font-size: 14px; line-height: 1.5;">

    <h2 style="margin-bottom: 4px;">Hives Needing Attention</h2>
    <p style="color: #6c757d; margin-top: 0;">Generated {{ now()->format('d M Y, H:i') }} ({{ config('app.timezone') }})</p>

    @if($isTest)
        <p style="background: #fff3cd; padding: 8px 12px; border-radius: 4px;">
            This is a test email sent from the report settings page. The SMTP settings are working.
        </p>
    @endif

    <p>
        Total hives: <strong>{{ $counts['all'] }}</strong> &nbsp;|&nbsp;
        Reporting normally: <strong>{{ $counts['normal'] }}</strong> &nbsp;|&nbsp;
        Needing attention: <strong>{{ $counts['attention'] }}</strong> &nbsp;|&nbsp;
        No data at all: <strong>{{ $counts['nodata'] }}</strong>
    </p>

    @if(count($hives) === 0)
        <p>No hive has a data type that has gone quiet for more than {{ $staleThresholdHours }}h.</p>
    @else
        <p>{{ count($hives) }} hive{{ count($hives) > 1 ? 's have' : ' has' }} at least one data type that hasn't reported in over {{ $staleThresholdHours }}h.</p>
    @endif

    <p>The full report, with charts, the list of affected hives and their farmers' contact details, is attached as a PDF.</p>

    <p style="margin-top: 20px;">
        <a href="{{ url('/admin/dashboard/hives?status=attention') }}">Open this list on the dashboard</a>
    </p>

</body>
</html>
