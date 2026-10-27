@extends('layouts.app')
@section('content')

<div class="relative">

  <div class="flex flex-row items-center justify-between mb-4">
    <h3 class="mx-2 font-bold py-1 text-green-600 text-lg">Bee Count Analysis</h3>
    <span class="text-sm text-gray-500 mr-2">Counts only &middot; no sensor or weather data in use</span>
  </div>

  <div class="bg-yellow-50 border-l-4 border-yellow-500 p-3 mb-5 text-sm text-gray-700">
    Conclusions here come from hive-entrance counts alone. A hive is judged against its own
    history and against the other hives at the same site. Where a site has fewer than three
    reporting hives, a site-wide cause cannot be ruled out and confidence is lowered accordingly.
  </div>

  <table class="w-full text-left text-sm">
    <thead class="bg-gray-100 text-gray-700">
      <tr>
        <th class="p-3">Farm</th>
        <th class="p-3">District</th>
        <th class="p-3">Hives</th>
        <th class="p-3">Open insights</th>
        <th class="p-3"></th>
      </tr>
    </thead>
    <tbody>
      @foreach ($farms as $farm)
      <tr class="border-b hover:bg-gray-50">
        <td class="p-3 font-semibold">{{ $farm->name }}</td>
        <td class="p-3">{{ $farm->district }}</td>
        <td class="p-3">{{ $farm->hives_count }}</td>
        <td class="p-3">
          @php $n = $openById[$farm->id] ?? 0; @endphp
          @if ($n > 0)
            <span class="px-2 py-1 rounded bg-red-100 text-red-700 font-semibold">{{ $n }}</span>
          @else
            <span class="text-gray-400">none</span>
          @endif
        </td>
        <td class="p-3">
          <a href="{{ url('/analysis/farm/' . $farm->id) }}"
             class="px-3 py-1 rounded bg-green-800 text-white hover:bg-green-700">Open</a>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>

</div>

@endsection
