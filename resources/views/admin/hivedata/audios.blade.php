@extends('layouts.app')
@section('content')

<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />


@php
    $hive_id = session('hive_id');
@endphp



@include('datanavbar')

<!-- Display the hive_id at the top of the page -->
<h1 style="text-align: left; font-weight: bold; font-size: 1em; margin-bottom: 20px; color: green;">Hive ID: {{ $hive_id }}</h1>

<div class="relative p-3 mt-10 overflow-x-auto shadow-md sm:rounded-lg">

    <table id="myTable" class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">
                    Hive ID
                </th>
                <th scope="col" class="px-6 py-3">
                   #
                </th>
                <th scope="col" class="px-6 py-3">
                    Audio
                </th>
                <th scope="col" class="px-6 py-3">
                    Date
                </th>
            </tr>
        </thead>
        <tbody>
            @php
                $count = 1
            @endphp
            @foreach($audios as $audio)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ $audio->hive_id }}
                    </th>
                    <td class="px-6 py-4">
                       {{ $count }}
                    </td>
                    <td class="px-6 py-4">
                        <audio controls>
                            <source src="{{ URL("hiveaudio/"."".$audio->path) }}" type="audio/mpeg">
                        </audio>
                    </td>
                    <td class="px-6 py-4">
                        {{ $audio->created_at }}
                    </td>
                </tr>
                @php
                    $count = $count + 1
                @endphp
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $audios->appends(['hive_id' => $hiveId])->links('vendor.pagination.simple-custom') }}
    </div>
</div>


@endsection
