@extends('layouts.app')
@section('content')

<div class="relative">

  <div class="flex flex-row items-center justify-between mb-4">
    <div>
      <h3 class="mx-2 font-bold py-1 text-green-600 text-lg">
        Hive <span class="font-extrabold">{{ $hive_id }}</span>
        <span class="text-sm font-normal text-gray-500">&middot; {{ $farm_name }}</span>
      </h3>
      @if ($class)
        <div class="mx-2 mt-1 text-sm">
          <span class="px-2 py-1 rounded bg-gray-100">{{ str_replace('_',' ', $class->size_class) }} size</span>
          <span class="px-2 py-1 rounded bg-gray-100">{{ str_replace('_',' ', $class->condition_class) }}</span>
          <span class="text-gray-500 ml-2">since {{ \Carbon\Carbon::parse($class->effective_from)->toDateString() }}</span>
        </div>
      @endif
    </div>
    <div>
      <h3 class='mx-2 font-bold py-1 text-green-600'>Select a date-range</h3>
      <div id="reportrange" class="border-2 border-green-800 rounded-lg"
           style="background:#fff;cursor:pointer;padding:5px 10px;border:1px solid #ccc;width:260px;">
        <i class="fa fa-calendar"></i>&nbsp;<span></span> <i class="fa fa-caret-down"></i>
      </div>
    </div>
  </div>

  @if ($baselineRolling)
    <div class="text-sm text-gray-600 mb-4 mx-2">
      Baseline {{ round($baselineRolling->base_daily, 1) }}
      over {{ $baselineRolling->days_used }} days
      ({{ \Carbon\Carbon::parse($baselineRolling->window_start)->toDateString() }}
       to {{ \Carbon\Carbon::parse($baselineRolling->window_end)->toDateString() }})
      @if ($baselineInitial)
        &middot; first-week baseline was {{ round($baselineInitial->base_daily, 1) }}
      @endif
    </div>
  @else
    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-3 mb-4 text-sm">
      This hive is not yet characterised, so no deviation can be judged. It needs several
      days of usable captures before a baseline exists.
    </div>
  @endif

  <div id="chartDaily"   style="width:100%;height:340px;"></div>
  <div id="chartZ"       style="width:100%;height:260px;margin-top:20px;"></div>
  <div id="chartProfile" style="width:100%;height:300px;margin-top:20px;"></div>

  <h4 class="font-bold text-green-700 mt-8 mb-2">Insight history</h4>
  @if ($insights->isEmpty())
    <p class="text-gray-500 text-sm mb-6">No insights recorded for this hive.</p>
  @else
    <table class="w-full text-left text-sm mb-8">
      <thead class="bg-gray-100 text-gray-700">
        <tr>
          <th class="p-2">Date</th><th class="p-2">Finding</th>
          <th class="p-2">Action</th><th class="p-2">Confidence</th>
          <th class="p-2">Escalate</th><th class="p-2">Basis</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($insights as $i)
        <tr class="border-b">
          <td class="p-2">{{ \Carbon\Carbon::parse($i->date)->toDateString() }}</td>
          <td class="p-2">{{ $i->message }}</td>
          <td class="p-2 font-semibold">{{ $i->action }}</td>
          <td class="p-2">{{ $i->confidence }}</td>
          <td class="p-2">{{ $i->escalate ? 'yes' : '' }}</td>
          <td class="p-2 text-gray-500">{{ $i->evidence }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  @endif

</div>

<script type="text/javascript">
$(function () {
  var hiveId  = {{ $hive_id }};
  var daily   = echarts.init(document.getElementById('chartDaily'));
  var zChart  = echarts.init(document.getElementById('chartZ'));
  var profile = echarts.init(document.getElementById('chartProfile'));

  var start = moment().subtract(29, 'days');
  var end   = moment();

  function load(start, end) {
    $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

    $.ajax({
      url: '/analysis/hive/' + hiveId + '/data',
      method: 'GET',
      data: { start: start.format('YYYY-MM-DD'), end: end.format('YYYY-MM-DD') },
      success: function (res) {

        daily.setOption({
          title: { text: 'Daily activity against baseline' },
          tooltip: { trigger: 'axis' },
          legend: {},
          xAxis: { type: 'category', data: res.dates },
          yAxis: { type: 'value', name: 'activity index' },
          series: [
            { name: 'Observed', type: 'line', data: res.total, connectNulls: false },
            { name: 'Expected', type: 'line', data: res.expected, lineStyle: { type: 'dashed' } }
          ]
        }, true);

        zChart.setOption({
          title: { text: 'Deviation from baseline (z) and trend (% per day)' },
          tooltip: { trigger: 'axis' },
          legend: {},
          xAxis: { type: 'category', data: res.dates },
          yAxis: [
            { type: 'value', name: 'z' },
            { type: 'value', name: '%/day' }
          ],
          series: [
            { name: 'z', type: 'line', data: res.z, connectNulls: false,
              markLine: { silent: true, data: [{ yAxis: -2.5 }, { yAxis: 3 }] } },
            { name: 'trend', type: 'line', yAxisIndex: 1, data: res.slope, connectNulls: false }
          ]
        }, true);

        if (res.profile) {
          var hours = Object.keys(res.profile).sort(function (a, b) { return a - b; });
          profile.setOption({
            title: { text: "This hive's normal day" },
            tooltip: { trigger: 'axis' },
            legend: {},
            xAxis: { type: 'category', data: hours, name: 'hour' },
            yAxis: { type: 'value', name: 'mean bees / frame' },
            series: [
              { name: 'q25',    type: 'line', data: hours.map(function (h) { return res.profile[h].q25; }) },
              { name: 'median', type: 'line', data: hours.map(function (h) { return res.profile[h].median; }), lineStyle: { width: 3 } },
              { name: 'q75',    type: 'line', data: hours.map(function (h) { return res.profile[h].q75; }) }
            ]
          }, true);
        }
      }
    });
  }

  $('#reportrange').daterangepicker({
    startDate: start, endDate: end,
    ranges: {
      'Last 7 Days':  [moment().subtract(6, 'days'), moment()],
      'Last 30 Days': [moment().subtract(29, 'days'), moment()],
      'This Month':   [moment().startOf('month'), moment().endOf('month')],
      'Last Month':   [moment().subtract(1,'month').startOf('month'), moment().subtract(1,'month').endOf('month')],
      'Last 3 Months':[moment().subtract(89, 'days'), moment()]
    }
  }, load);

  load(start, end);
});
</script>

@endsection
