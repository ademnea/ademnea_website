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

<div style="text-align: right;">
    <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search ..">
</div>

<!-- Display the hive_id at the top of the page -->
<h1 style="text-align: left; font-weight: bold; font-size: 1em; margin-bottom: 20px; color: green;">Hive : {{ $hive_id }}</h1>

<div class="relative p-3 mt-10 overflow-x-auto shadow-md sm:rounded-lg">

    <div class="card-header">
        <div class="row">
            <div class="col col-9"><b>Pick Date Range</b></div>
            <div class="col col-3">
                <div>
                <h3 class='mx-2 font-bold py-1 text-green-600'>Select a date-range</h3>
                <div id="reportrange" class="border-2 border-green-800 rounded-lg hover:bg-green-800"
                    style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 260px;">
                    <i class="fa fa-calendar"></i>&nbsp;
                    <span></span> <i class="fa fa-caret-down"></i>
                </div>
                </div>
            </div>
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
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, cb);

            cb(start, end);

            
            $('#myTable').DataTable({
                responsive: true
            });

            function fetchData(start, end) {
                var startDate = start.format('YYYY-MM-DD HH:mm:ss');
                var endDate = end.format('YYYY-MM-DD HH:mm:ss');
                var hiveId = '{{ $hive_id }}';

                $.ajax({
                    url: '/hive_data/temperature_data/' + hiveId,
                    method: 'GET',
                    data: {
                        start: startDate,
                        end: endDate,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        console.log(response);
                        location.reload();
                    }
                });
            }

            $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
                fetchData(picker.startDate, picker.endDate);
            });
        });
    </script>

    <br>

    <div class="flex justify-end mb-4">
        <a href="{{ route('temperature.export', ['hive_id' => $hive_id]) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
            Download
        </a>
    </div>
    

    <table id="myTable" class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">
                    #
                </th>
                <!-- <th scope="col" class="px-6 py-3">
                    Hive ID
                </th> -->
                <th scope="col" class="px-6 py-3">
                 Interior (°C)
                </th>
                {{-- <th scope="col" class="px-6 py-3">
                 Brood Section (°C)
                </th> --}}
                <th scope="col" class="px-6 py-3">
                 Exterior (°C)
                </th>
                <th scope="col" class="px-6 py-3">
                    Date
                </th>
            </tr>
        </thead>
        <tbody>
        @php $count = 1; @endphp
        @foreach($temperatures as $temperature)
            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $count }}
                </th>
                <!-- <td class="px-6 py-4">
                {{ $temperature->hive_id }}
                </td> -->
                <td class="px-6 py-4">
                {{ explode('*', $temperature->record)[0] }}
                </td>
                {{-- <td class="px-6 py-4">
                {{ explode('*', $temperature->record)[1] }}
                </td> --}}
                <td class="px-6 py-4">
                {{ explode('*', $temperature->record)[2] }}
                </td>
                <td class="px-6 py-4">
                {{ $temperature->created_at }}
                </td>
            </tr>
            @php $count++; @endphp
        @endforeach
        </tbody>
    </table>
</div>

@endsection

@section('page_scripts')
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

<script>

//   $(document).ready(function() {
//   $('#myTable').DataTable({
//      responsive: true,
//   });
// });

</script>

    <!-- Display the temperature table from date range picked using this code -->
   <script type="text/javascript">
        $(function() {
        
          var start = moment().subtract(1, 'days'); //by default , just display data for the previous day or 24 hours
          var end = moment();
          var hiveId = {{ $hive_id }}; 
        
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
                },

                success: function(response) {
                console.log(response);

                // Refresh the page after the AJAX request completes
                location.reload();
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

<script>
function myFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("myTable");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[0];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}

$(document).ready( function () {
    $('#myTable').DataTable();
} );
</script>
@endsection
