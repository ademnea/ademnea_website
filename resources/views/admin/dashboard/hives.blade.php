@extends('layouts.app')
@section('content')

<div class="relative p-3 mt-2 overflow-x-auto shadow-md sm:rounded-lg">
    <div class="container-fluid py-4">

        <div class="d-sm-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0">{{ $statusLabels[$status] }}</h4>
                <p class="text-muted mb-0">
                    @if($status === 'normal')
                        Hives with at least one data type reporting, and nothing older than {{ $staleThresholdHours }}h.
                    @elseif($status === 'attention')
                        Hives with at least one data type that hasn't reported in over {{ $staleThresholdHours }}h.
                    @elseif($status === 'nodata')
                        Hives that have never sent a single reading of any kind.
                    @else
                        Every beehive, with the farm and farmer it belongs to.
                    @endif
                </p>
            </div>
            <div>
                @if($status === 'attention')
                    <a href="{{ url('/admin/report-settings') }}" class="btn btn-outline-danger btn-sm">Email this report</a>
                @endif
                <a href="{{ url('/admin/dashboard') }}" class="btn btn-outline-secondary btn-sm">Back to dashboard</a>
            </div>
        </div>

        <ul class="nav nav-pills mb-3">
            @foreach($statusLabels as $key => $label)
                <li class="nav-item">
                    <a class="nav-link {{ $status === $key ? 'active' : '' }}" href="{{ url('/admin/dashboard/hives?status=' . $key) }}">
                        {{ $label }} ({{ $counts[$key] }})
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="hivesTable" class="table">
                        <thead>
                            <tr>
                                <th>Hive</th>
                                <th>Farm</th>
                                <th>District</th>
                                <th>Farmer</th>
                                <th>Telephone</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Not Reporting</th>
                                <th></th>
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
                                        @if($row['status'] === 'normal')
                                            <span class="badge badge-opacity-success">Normal</span>
                                        @elseif($row['status'] === 'attention')
                                            <span class="badge badge-opacity-danger">Needs attention</span>
                                        @else
                                            <span class="badge badge-opacity-warning">No data</span>
                                        @endif
                                    </td>
                                    <td>
                                        @forelse($row['stale_types'] as $stale)
                                            <span class="badge badge-opacity-danger" title="Last reading {{ $stale['ago'] }}">{{ $stale['label'] }}: {{ $stale['ago'] }}</span>
                                        @empty
                                            <span class="text-muted">—</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        <a href="{{ url('/hive_data/' . $row['hive_id']) }}" class="btn btn-outline-primary btn-sm">Hive Data</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection

@section('page_scripts')
<script>
    $(document).ready(function () {
        $('#hivesTable').DataTable({
            pageLength: 25,
            language: { emptyTable: 'No hives in this category.' }
        });
    });
</script>
@endsection
