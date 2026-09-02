@extends('layouts.app')
@section('content')


<div class="relative ">

</head>
<body>
<!--
<div>
<a href="{{ url('admin/hive') }}" class="inline-block px-2 py-2 rounded-sm text-white bg-blue-700 hover:bg-blue-500">Back</a>
</div> -->

  {{-- data nav bar goes here --}}
  @include('datanavbar')

  <!-- Choose date range -->
<div class="flex flex-row space-x-4 items-center justify-between h-8 mb-4 bg-white">
    <div>

        <h3 class='mx-2 font-bold py-1 text-green-600'>Hive : <span class="font-extrabold">{{ $hive_id }}</span></h3>
    </div>

    <div>
        <h3 class='mx-2 font-bold py-1 text-green-600'>Select a date-range</h3>
        <!-- Date range picker -->
        <div id="reportrange" class="border-2 border-green-800 rounded-lg hover:bg-green-800"
            style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 260px">
            <i class="fa fa-calendar"></i>&nbsp;
            <span></span> <i class="fa fa-caret-down"></i>
        </div>
    </div>

    <script type="text/javascript">
        $(function() {

            var start = moment().subtract(29, 'days');
            var end = moment();

            function cb(start, end) {
                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }

            $('#reportrange').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')]
                }
            }, cb);

            cb(start, end);

        });
    </script>
</div>
</div>

</body>




    <!-- Display the temperature graph -->
    <script type="text/javascript">
    $(function() {
      var start = moment().subtract(6, 'days'); // Default to last 7 days
      var end = moment();
      var hiveId = {{ $hive_id }};
      var myChart = echarts.init(document.getElementById('chart'));

      function cb(start, end) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

        // Format dates for the server
        var startDate = start.format('YYYY-MM-DD HH:mm:ss');
        var endDate = end.format('YYYY-MM-DD HH:mm:ss');

        // Send AJAX request to server
        $.ajax({
            url: '/hive_data/carbondioxide_data/' + hiveId,
            method: 'GET',
            data: {
                start: startDate,
                end: endDate,
                table: 'hive_carbondioxide'
            },
            success: function(response) {
                console.log("Data received:", response); // Add this for debugging
                myChart.setOption({
                    title: {
                        text: 'Carbon Dioxide Levels'
                    },
                    tooltip: {
                        trigger: 'axis'
                    },
                    legend: {},
                    toolbox: {
                        show: true,
                        feature: {
                            dataZoom: {
                                show: false,
                                yAxisIndex: 'none'
                            },
                            dataView: { show: false, readOnly: false },
                            magicType: { show: false, type: ['line', 'bar'] },
                            restore: { show: false },
                            saveAsImage: { show: true }
                        }
                    },
                    xAxis: {
                        type: 'category',
                        boundaryGap: false,
                        data: response.dates
                    },
                    yAxis: {
                        type: 'value',
                        axisLabel: {
                            formatter: '{value} ppm'
                        },
                        splitNumber: 10
                    },
                    series: [
                        {
                            name: 'Carbon Dioxide Levels',
                            type: 'line',
                            data: response.carbonLevels,
                            markPoint: {
                                data: [
                                    { type: 'max', name: 'Max' },
                                    { type: 'min', name: 'Min' }
                                ]
                            },
                            markLine: {
                                data: [{ type: 'average', name: 'Avg' }]
                            }
                        }
                    ]
                });
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", error);
                console.log("Response:", xhr.responseText);
            }
        });
      }

      $('#reportrange').daterangepicker({
        ranges: {
           'Today': [moment().startOf('day'), moment().endOf('day')],
           'Yesterday': [moment().subtract(1, 'days').startOf('day'), moment().subtract(1, 'days').endOf('day')],
           'Last 7 Days': [moment().subtract(6, 'days').startOf('day'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days').startOf('day'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
      }, cb);

      // Initialize with default date range
      cb(start, end);
    });
    </script>


        <div class='bg-white mx-3 p-1 mt-2'>
              <div id="chart" style="width: 100%; height: 480px;" class='p-2'></div>
                <script>
                // JavaScript code to create and configure the chart
                var myChart = echarts.init(document.getElementById('chart'));
                </script>
        </div>


</div>

@endsection
