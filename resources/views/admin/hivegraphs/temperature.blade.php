@extends('layouts.app')
@section('content')


<div class="relative ">

<body>

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
            style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 260px; ">
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

  var start = moment().subtract(6, 'days'); //by default , just display data for the last 7 days
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
        url: '/hive_data/temperature_data/' + hiveId,
        method: 'GET',
        data: {
            start: startDate,
            end: endDate,
            table: 'hive_temperatures' // name of the table you want to fetch data from
        },
        success: function(response) {

            // handle the response data
            myChart.setOption({
              // chart configuration options here
                title: {
                            text: 'Temperatures'
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
                          data: response.dates // assuming you returned the dates under the key 'dates'
                      },
                yAxis: {
                          type: 'value',
                          min: 10,
                          max: 40,
                          axisLabel: {
                          formatter: '{value} °C'
                          },
                          splitNumber: 10
                      },
                series: [
                //   {
                //         name: 'Brood Section',
                //         type: 'line',
                //         data: response.broodSection,
                //         markPoint: {
                //                     data: [
                //                           { type: 'max', name: 'Max' },
                //                           { type: 'min', name: 'Min' }
                //                           ]
                //                     },
                //         markLine: {
                //                     data: [{ type: 'average', name: 'Avg' }]
                //                    },
                //                   //  color: 'red' 
                //       },
                    {
                        name: 'Exterior',
                        type: 'line',
                        data: response.exterior,
                        markPoint: {
                                    data: [
                                          { type: 'max', name: 'Max' },
                                          { type: 'min', name: 'Min' }
                                          ]
                                    },
                        markLine: {
                                    data: [{ type: 'average', name: 'Avg' }]
                                   },
                        
                        // color: 'green' 
                       
                    },
                   
                      {
                        name: 'Interior',
                        type: 'line',
                        data: response.honeySection,
                        markPoint: {
                        data: [
                              { type: 'max', name: 'Max' },
                              { type: 'min', name: 'Min' }
                            ]
                          },
                          markLine: {
                            data: [{ type: 'average', name: 'Avg' }]
                          },
                          // color: 'blue' 
                    },
                ]
            });
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

cb(start, end);
});
</script>

      

              <div class='bg-white'>
                    <div id="chart" style="width: 100%; height: 480px;" class='p-1'></div>
                      <script>
                      // JavaScript code to create and configure the chart
                      var myChart = echarts.init(document.getElementById('chart'));
                      </script>
              </div>
      

</div>



@endsection
