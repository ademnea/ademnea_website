<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AdeMNEA hive report</title>
    <style>
        @page { margin: 28px 32px 40px 32px; }
        body { font-family: "DejaVu Sans", sans-serif; color: #212529; font-size: 10px; line-height: 1.4; }
        h1 { font-size: 18px; margin: 0 0 2px 0; color: #146c43; }
        h2 { font-size: 13px; margin: 18px 0 6px 0; color: #146c43; }
        .muted { color: #6c757d; }
        .test { background: #fff3cd; padding: 6px 10px; margin: 8px 0; }
        .footer { position: fixed; bottom: -26px; left: 0; right: 0; font-size: 8px; color: #6c757d; }

        .kpis { width: 100%; border-collapse: separate; border-spacing: 6px 0; margin: 10px -6px 0 -6px; }
        .kpi { border: 1px solid #dee2e6; padding: 8px 10px; width: 25%; }
        .kpi .num { font-size: 20px; font-weight: bold; }
        .kpi .lbl { color: #6c757d; }

        .stack { width: 100%; border-collapse: collapse; height: 18px; }
        .stack td { height: 18px; padding: 0; }
        .legend span.sw { display: inline-block; width: 9px; height: 9px; margin: 0 3px 0 10px; }

        .cols { width: 100%; border-collapse: collapse; }
        .cols > tbody > tr > td { width: 50%; vertical-align: top; padding: 0 10px 0 0; }

        .bars { width: 100%; border-collapse: collapse; }
        .bars td { padding: 2px 0; vertical-align: middle; }
        .bars .lbl { width: 28%; padding-right: 6px; }
        .bars .track { width: 62%; background: #f1f3f5; }
        .bars .bar { height: 11px; }
        .bars .val { width: 10%; padding-left: 6px; font-weight: bold; }

        table.data { width: 100%; border-collapse: collapse; }
        table.data th { background: #f1f3f5; text-align: left; }
        table.data th, table.data td { border: 1px solid #dee2e6; padding: 4px 5px; vertical-align: top; }
        table.data tr { page-break-inside: avoid; }

        .fresh { background: #d1e7dd; }
        .stale { background: #f8d7da; }
        .none { background: #e9ecef; color: #6c757d; }
    </style>
</head>
<body>
@php
    $statusColors = ['normal' => '#198754', 'attention' => '#dc3545', 'nodata' => '#adb5bd'];
    $statusNames = ['normal' => 'Reporting normally', 'attention' => 'Needing attention', 'nodata' => 'No data at all'];
    $maxType = max(1, max($staleByType ?: [0]));
    $maxDistrict = max(1, max($byDistrict ?: [0]));
@endphp

    <div class="footer">AdeMNEA hive report &middot; generated {{ now()->format('d M Y, H:i') }} ({{ config('app.timezone') }})</div>

    <h1>Hive Health Report</h1>
    <div class="muted">Generated {{ now()->format('d M Y, H:i') }} ({{ config('app.timezone') }}). A data type is flagged when it hasn't reported in over {{ $staleThresholdHours }}h.</div>

    @if($isTest)
        <div class="test">This is a test report sent from the report settings page.</div>
    @endif

    {{-- Summary --}}
    <table class="kpis">
        <tr>
            <td class="kpi"><div class="num">{{ $counts['all'] }}</div><div class="lbl">Total hives</div></td>
            <td class="kpi"><div class="num" style="color: {{ $statusColors['normal'] }}">{{ $counts['normal'] }}</div><div class="lbl">Reporting normally</div></td>
            <td class="kpi"><div class="num" style="color: {{ $statusColors['attention'] }}">{{ $counts['attention'] }}</div><div class="lbl">Needing attention</div></td>
            <td class="kpi"><div class="num" style="color: #6c757d">{{ $counts['nodata'] }}</div><div class="lbl">No data at all</div></td>
        </tr>
    </table>

    {{-- Chart: status breakdown --}}
    <h2>Hive status breakdown</h2>
    @if($counts['all'] > 0)
        <table class="stack">
            <tr>
                @foreach($statusColors as $status => $color)
                    @if($counts[$status] > 0)
                        <td style="width: {{ round($counts[$status] / $counts['all'] * 100, 2) }}%; background: {{ $color }};"></td>
                    @endif
                @endforeach
            </tr>
        </table>
        <div class="legend" style="margin-top: 4px;">
            @foreach($statusColors as $status => $color)
                <span class="sw" style="background: {{ $color }};"></span>{{ $statusNames[$status] }}: {{ $counts[$status] }} ({{ round($counts[$status] / $counts['all'] * 100) }}%)
            @endforeach
        </div>
    @else
        <div class="muted">No hives registered.</div>
    @endif

    @if(count($hives) > 0)
        {{-- Charts: which data types and which districts --}}
        <table class="cols">
            <tr>
                <td>
                    <h2>Data types not reporting</h2>
                    <div class="muted" style="margin-bottom: 4px;">Number of hives needing attention where each data type has gone quiet</div>
                    <table class="bars">
                        @foreach($staleByType as $label => $n)
                            <tr>
                                <td class="lbl">{{ $label }}</td>
                                <td class="track">
                                    @if($n > 0)<div class="bar" style="width: {{ round($n / $maxType * 100, 2) }}%; background: #dc3545;"></div>@endif
                                </td>
                                <td class="val">{{ $n }}</td>
                            </tr>
                        @endforeach
                    </table>
                </td>
                <td>
                    <h2>Hives needing attention by district</h2>
                    <div class="muted" style="margin-bottom: 4px;">Where the affected hives are located</div>
                    <table class="bars">
                        @foreach($byDistrict as $district => $n)
                            <tr>
                                <td class="lbl">{{ $district }}</td>
                                <td class="track"><div class="bar" style="width: {{ round($n / $maxDistrict * 100, 2) }}%; background: #fd7e14;"></div></td>
                                <td class="val">{{ $n }}</td>
                            </tr>
                        @endforeach
                    </table>
                </td>
            </tr>
        </table>

        {{-- Table: contacts --}}
        <h2 style="page-break-before: always;">Hives needing attention</h2>
        <table class="data">
            <thead>
                <tr>
                    <th>Hive</th>
                    <th>Farm</th>
                    <th>District</th>
                    <th>Farmer</th>
                    <th>Telephone</th>
                    <th>Email</th>
                    <th>Not reporting</th>
                </tr>
            </thead>
            <tbody>
                @foreach($hives as $row)
                    <tr>
                        <td>Hive {{ $row['hive_id'] }}</td>
                        <td>{{ $row['farm_name'] }}</td>
                        <td>{{ $row['farm_district'] }}</td>
                        <td>{{ $row['farmer_name'] }}</td>
                        <td>{{ $row['farmer_telephone'] }}</td>
                        <td>{{ $row['farmer_email'] }}</td>
                        <td>
                            @foreach($row['stale_types'] as $stale)
                                {{ $stale['label'] }} (last {{ $stale['ago'] }})@if(!$loop->last), @endif
                            @endforeach
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Table: last reading per data type --}}
        <h2>Last reading per data type</h2>
        <div class="legend" style="margin-bottom: 4px;">
            <span class="sw fresh" style="margin-left: 0;"></span>Within {{ $staleThresholdHours }}h
            <span class="sw stale"></span>Older than {{ $staleThresholdHours }}h
            <span class="sw none"></span>Never reported
        </div>
        <table class="data">
            <thead>
                <tr>
                    <th>Hive</th>
                    @foreach($typeLabels as $label)
                        <th>{{ $label }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($hives as $row)
                    <tr>
                        <td>Hive {{ $row['hive_id'] }}</td>
                        @foreach($typeLabels as $key => $label)
                            @php $type = $row['types'][$key]; @endphp
                            <td class="{{ $type['status'] }}">{{ $type['ago'] ?? 'never' }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <h2>Hives needing attention</h2>
        <div>No hive has a data type that has gone quiet for more than {{ $staleThresholdHours }}h.</div>
    @endif

</body>
</html>
