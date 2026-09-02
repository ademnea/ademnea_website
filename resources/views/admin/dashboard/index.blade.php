@extends('layouts.app')
@section('content')

<!-- the four cards above on the dashboard -->
<div class="relative p-3 mt-2 overflow-x-auto shadow-md sm:rounded-lg">
    <div class="container-fluid py-4">
        <div class="row min-vh-80 h-100">
            <div class="col-12">
                <div class="row">

                    <!-- Total Hives -->
                    <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-9">
                                        <div class="d-flex align-items-center align-self-start">
                                            <h3 class="mb-0 text-info">{{$totalHives}}</h3>
                                        </div>
                                    </div>
                                    <div class="col-3 text-end">
                                        <i class="mdi mdi-cube-outline mdi-36px text-info"></i>
                                    </div>
                                </div>
                                <h5 class="text-muted font-weight-normal">Total Hives</h5>
                                <p>This represents all beehives managed across farms,
                                   each tracked individually for productivity and health.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Reporting Normally -->
                    <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-9">
                                        <div class="d-flex align-items-center align-self-start">
                                            <h3 class="mb-0 text-success">{{$normalHiveCount}}</h3>
                                        </div>
                                    </div>
                                    <div class="col-3 text-end">
                                        <i class="mdi mdi-check-circle-outline mdi-36px text-success"></i>
                                    </div>
                                </div>
                                <h5 class="text-muted font-weight-normal">Reporting Normally</h5>
                                <p>Hives with at least one data type reporting, and nothing
                                   older than {{ $staleThresholdHours }}h.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Needing Attention -->
                    <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-9">
                                        <div class="d-flex align-items-center align-self-start">
                                            <h3 class="mb-0 text-danger">{{$staleHiveCount}}</h3>
                                        </div>
                                    </div>
                                    <div class="col-3 text-end">
                                        <i class="mdi mdi-alert-circle-outline mdi-36px text-danger"></i>
                                    </div>
                                </div>
                                <h5 class="text-muted font-weight-normal">Needing Attention</h5>
                                <p>Hives with at least one data type that hasn't reported
                                   in over {{ $staleThresholdHours }}h.</p>
                            </div>
                        </div>
                    </div>

                    <!-- No Data At All -->
                    <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-9">
                                        <div class="d-flex align-items-center align-self-start">
                                            <h3 class="mb-0 text-warning">{{$noDataHiveCount}}</h3>
                                        </div>
                                    </div>
                                    <div class="col-3 text-end">
                                        <i class="mdi mdi-help-circle-outline mdi-36px text-warning"></i>
                                    </div>
                                </div>
                                <h5 class="text-muted font-weight-normal">No Data At All</h5>
                                <p>Hives that have never sent a single reading of any kind -
                                   likely never deployed or not yet connected.</p>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-sm-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h4 class="mb-0">Hive Data Health</h4>
                                        <p class="text-muted mb-0">Time since last reading received per hive. Anything older than {{ $staleThresholdHours }}h is flagged.</p>
                                    </div>
                                    @if($staleHiveCount > 0)
                                        <span class="badge badge-opacity-danger">{{ $staleHiveCount }} hive{{ $staleHiveCount > 1 ? 's' : '' }} need attention</span>
                                    @else
                                        <span class="badge badge-opacity-success">All hives reporting normally</span>
                                    @endif
                                </div>

                                @php
                                    $healthColumnLabels = [
                                        'temperature' => 'Temp',
                                        'humidity' => 'Humidity',
                                        'carbondioxide' => 'CO2',
                                        'weight' => 'Weight',
                                        'voc' => 'VOC',
                                        'photos' => 'Photos',
                                        'videos' => 'Videos',
                                        'audios' => 'Audio',
                                    ];
                                @endphp

                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Hive</th>
                                                <th>Farm</th>
                                                @foreach($dataTypeKeys as $key)
                                                    <th>{{ $healthColumnLabels[$key] }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($hiveHealth as $row)
                                                <tr>
                                                    <td>Hive {{ $row['hive_id'] }}</td>
                                                    <td>{{ $row['farm_name'] }}</td>
                                                    @foreach($dataTypeKeys as $key)
                                                        @php $cell = $row['types'][$key]; @endphp
                                                        <td>
                                                            @if($cell['status'] === 'fresh')
                                                                <span class="badge badge-opacity-success">{{ $cell['ago'] }}</span>
                                                            @elseif($cell['status'] === 'stale')
                                                                <span class="badge badge-opacity-danger">{{ $cell['ago'] }}</span>
                                                            @else
                                                                <span class="badge badge-opacity-warning">No data</span>
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <p class="text-muted mb-0" style="font-size: 0.85rem;">"No data" means that hive has never sent this data type - it may not have that sensor installed rather than a fault.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection
