@extends('layouts.app')

@section('content')

<div class="relative">

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
            style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 260px;">
            <i class="fa fa-calendar"></i>&nbsp;
            <span></span> <i class="fa fa-caret-down"></i>
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
      });
    </script>
  </body>

    <!-- Amplitude vs Frequency Chart -->
    <div class='bg-white mb-4'>
      <h3 class='mx-2 font-bold py-1 text-green-600'>Amplitude vs Frequency</h3>
      <canvas id="amplitudeFrequencyChart" width="400" height="200"></canvas>
    </div>

    <!-- Frequency Spectrum Chart -->
    <div class='bg-white'>
      <h3 class='mx-2 font-bold py-1 text-green-600'>Frequency Spectrum</h3>
      <canvas id="frequencySpectrumChart" width="400" height="200"></canvas>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.0/papaparse.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const parsedData = {!! $data !!};  // Data passed from the controller

            function parseDataAndPlot(data) {
                const parsed = Papa.parse(data, { header: true }).data;

                // Extract amplitude and frequency data for plotting
                const frequencyX = parsed.map(row => parseFloat(row['Frequency_X']));
                const amplitudeY = parsed.map(row => parseFloat(row['Amplitude_Y']));

                const ctx1 = document.getElementById('amplitudeFrequencyChart').getContext('2d');
                const amplitudeFrequencyChart = new Chart(ctx1, {
                    type: 'line',
                    data: {
                        labels: frequencyX,
                        datasets: [{
                            label: 'Amplitude Y',
                            data: amplitudeY,
                            borderColor: 'green',
                            fill: false,
                            pointRadius: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            x: {
                                display: true,
                                title: {
                                    display: true,
                                    text: 'Frequency'
                                }
                            },
                            y: {
                                display: true,
                                title: {
                                    display: true,
                                    text: 'Amplitude'
                                }
                            }
                        }
                    }
                });

                // Create the second chart for Frequency Spectrum
                const fftAmplitudeY = parsed.map(row => parseFloat(row['FFT_Amplitude_Y']));
                const ctx2 = document.getElementById('frequencySpectrumChart').getContext('2d');
                const frequencySpectrumChart = new Chart(ctx2, {
                    type: 'line',
                    data: {
                        labels: frequencyX,
                        datasets: [{
                            label: 'FFT Amplitude Y',
                            data: fftAmplitudeY,
                            borderColor: 'green',
                            fill: false,
                            pointRadius: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            x: {
                                display: true,
                                title: {
                                    display: true,
                                    text: 'Frequency'
                                }
                            },
                            y: {
                                display: true,
                                title: {
                                    display: true,
                                    text: 'FFT Amplitude'
                                }
                            }
                        }
                    }
                });
            }

            // Initial data fetch and plot
            parseDataAndPlot(parsedData);

            // Automatically refresh the chart every 60 seconds
            // setInterval(function() {
            //     fetch(`/api/hivevibrations/${{{ $hive_id }}}`)  // Call the API to fetch updated data
            //         // .then(response => response.json())
            //         .then(data => {
            //             parseDataAndPlot(data);  // Re-plot the chart with new data
            //         })
            //         .catch(error => console.error('Error fetching new data:', error));
            // }, 60000); // 60 seconds interval
        });
    </script>
  </div>

@endsection
