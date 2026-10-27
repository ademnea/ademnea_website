@extends('layouts.app')
@section('content')

<div class="relative">

  <div class="flex flex-row items-center justify-between mb-4">
    <div>
      <h3 class="mx-2 font-bold py-1 text-green-600 text-lg">
        Apiary : <span class="font-extrabold">{{ $farm_name }}</span>
      </h3>
      <span id="modeLabel" class="mx-2 text-sm text-gray-500"></span>
    </div>
    <div>
      <h3 class='mx-2 font-bold py-1 text-green-600'>Select a date-range</h3>
      <div id="reportrange" class="border-2 border-green-800 rounded-lg"
           style="background:#fff;cursor:pointer;padding:5px 10px;border:1px solid #ccc;width:260px;">
        <i class="fa fa-calendar"></i>&nbsp;<span></span> <i class="fa fa-caret-down"></i>
      </div>
    </div>
  </div>

  {{-- hive cards --}}
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    @foreach ($hives as $h)
      @php $c = $classes[$h->id] ?? null; @endphp
      <div class="border rounded-lg p-4 bg-white shadow-sm">
        <div class="flex justify-between items-start">
          <div class="font-bold text-gray-800">Hive {{ $h->id }}</div>
          <a href="{{ url('/analysis/hive/' . $h->id) }}"
             class="text-xs px-2 py-1 rounded bg-green-800 text-white hover:bg-green-700">Detail</a>
        </div>
        @if ($c)
          <div class="mt-2 text-sm">
            <span class="px-2 py-1 rounded bg-gray-100">{{ str_replace('_',' ', $c->size_class) }} size</span>
            @php
              $tone = in_array($c->condition_class, ['critical','silent']) ? 'bg-red-100 text-red-700'
                    : ($c->condition_class === 'declining' ? 'bg-yellow-100 text-yellow-800'
                    : 'bg-green-100 text-green-800');
            @endphp
            <span class="px-2 py-1 rounded {{ $tone }}">{{ str_replace('_',' ', $c->condition_class) }}</span>
          </div>
          <div class="mt-2 text-xs text-gray-500">{{ $c->evidence }}</div>
        @else
          <div class="mt-2 text-sm text-gray-400">Not yet characterised</div>
        @endif
      </div>
    @endforeach
  </div>

  <div id="apiaryTotals" style="width:100%;height:340px;"></div>
  <div id="apiaryShare"  style="width:100%;height:300px;margin-top:20px;"></div>

  {{-- insights --}}
  <h4 class="font-bold text-green-700 mt-8 mb-2">Insights, last 7 days</h4>
  @if ($insights->isEmpty())
    <p class="text-gray-500 text-sm mb-6">Nothing to report. All hives within their expected range.</p>
  @else
    <table class="w-full text-left text-sm mb-8">
      <thead class="bg-gray-100 text-gray-700">
        <tr>
          <th class="p-2">Date</th><th class="p-2">Hive</th><th class="p-2">Finding</th>
          <th class="p-2">Action</th><th class="p-2">Confidence</th><th class="p-2">Basis</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($insights as $i)
        <tr class="border-b">
          <td class="p-2">{{ \Carbon\Carbon::parse($i->date)->toDateString() }}</td>
          <td class="p-2">{{ $i->hive_id }}</td>
          <td class="p-2">{{ $i->message }}</td>
          <td class="p-2 font-semibold">{{ $i->action }}</td>
          <td class="p-2">{{ $i->confidence }}</td>
          <td class="p-2 text-gray-500">{{ $i->evidence }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  @endif

</div>

<script type="text/javascript">
$(function () {
  var farmId = {{ $farm->id }};
  var totals = echarts.init(document.getElementById('apiaryTotals'));
  var share  = echarts.init(document.getElementById('apiaryShare'));

  var start = moment().subtract(29, 'days');
  var end   = moment();

  function load(start, end) {
    $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

    $.ajax({
      url: '/analysis/farm/' + farmId + '/data',
      method: 'GET',
      data: { start: start.format('YYYY-MM-DD'), end: end.format('YYYY-MM-DD') },
      success: function (res) {
        $('#modeLabel').text(
          res.mode === 'peer_compared'
            ? 'Peer-compared: three or more hives reporting, so site-wide causes can be separated out.'
            : 'Solo mode: too few reporting hives here to rule out a site-wide cause.'
        );

        totals.setOption({
          title: { text: 'Daily activity by hive' },
          tooltip: { trigger: 'axis' },
          legend: {},
          xAxis: { type: 'category', data: res.dates },
          yAxis: { type: 'value', name: 'activity index' },
          series: res.series.map(function (s) {
            return { name: 'Hive ' + s.hive_id, type: 'line', data: s.total, connectNulls: false };
          })
        }, true);

        share.setOption({
          title: { text: 'Share of apiary activity' },
          tooltip: { trigger: 'axis' },
          legend: {},
          xAxis: { type: 'category', data: res.dates },
          yAxis: { type: 'value', name: 'share', max: 1 },
          series: res.series.map(function (s) {
            return { name: 'Hive ' + s.hive_id, type: 'line', data: s.share, connectNulls: false };
          })
        }, true);
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
