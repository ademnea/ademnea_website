
<div class="flex flex-row mt-10 space-x-4 items-center justify-center h-16 mb-2 px-10 bg-white">

    {{-- we need to add this hive id to session to pick it once back is pressed. --}}
        <div>
        <a href="{{ url('admin/hive?farm_id=' . session('farm_id')) }}" class="inline-block px-2 py-2 rounded-sm text-white bg-gray-700 hover:bg-gray-500">Back</a>
        </div>


    {{-- graphs dropdown start --}}
    <button id="dropdownHoverButton" data-dropdown-toggle="dropdownHover" data-dropdown-trigger="hover"
        class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-4 py-2.5 text-center inline-flex items-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800"
        type="button">Graphs <svg class="w-4 h-4 ml-2" aria-hidden="true" fill="none" stroke="currentColor"
            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg></button>
    <!-- Dropdown menu -->
    <div id="dropdownHover"
        class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
        <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownHoverButton">
            <li>
                <a href="{{ url('/hive_data/temperature_data_default/' . $hive_id ) }}"
                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Temperature</a>
            </li>
            <li>
                <a href="{{ url('/hive_data/humidity_data_default/' . $hive_id) }}"
                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Humidity</a>
            </li>
            <li>
                <a href="{{ url('/hive_data/carbondioxide_data_default/' . $hive_id ) }}"
                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Carbondioxide</a>
            </li>
            <li>
                <a href="{{ url('/hive_data/weight_data_default/' . $hive_id) }}"
                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Weight</a>
            </li>
            <li>
                <a href="{{ url('/hive_data/tempHumidity_data_default/' . $hive_id) }}"
                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">TemperatureHumidity</a>
            </li>
            @if(isset($hive_id))
                <li>
                    <a href="{{ url('/hive_data/hiveVibration_data_default/' . $hive_id) }}"
                       class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                        Hive Vibrations
                    </a>
                </li>
            @else
                <li>
                    <a href="#" class="block px-4 py-2 text-red-500">Hive ID Missing</a>
                </li>
            @endif


        </ul>
    </div>

    {{-- raw data dropdown start --}}
    <button id="dropdownHoverButton2" data-dropdown-toggle="dropdownHover2" data-dropdown-trigger="hover"
        class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-4 py-2.5 text-center inline-flex items-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800"
        type="button">Raw Data<svg class="w-4 h-4 ml-2" aria-hidden="true" fill="none" stroke="currentColor"
            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg></button>
    <!-- Dropdown menu -->
    <div id="dropdownHover2"
        class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
        <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownHoverButton2">
            <li>
                <a href="{{ url('/admin/temperaturedata') }}?hive_id={{$hive_id}}"
                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Temperature</a>
            </li>
            <li>
                <a href="{{ url('/admin/humiditydata') }}?hive_id={{$hive_id}}"
                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Humidity</a>
            </li>
            <li>
                <a href="{{ url('/admin/carbondioxidedata') }}?hive_id={{$hive_id}}"
                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Carbondioxide</a>
            </li>
            <li>
                <a href="{{ url('/admin/weightdata') }}?hive_id={{$hive_id}}"
                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Weight</a>
            </li>
            <li>
                <a href="{{ url('/admin/vocdata') }}?hive_id={{$hive_id}}"
                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">VOC</a>
            </li>

        </ul>
    </div>

    {{-- Media dropdown start --}}
    <button id="dropdownHoverButton3" data-dropdown-toggle="dropdownHover3" data-dropdown-trigger="hover"
        class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-4 py-2.5 text-center inline-flex items-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800"
        type="button">
        Media
        <svg class="w-4 h-4 ml-2" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg></button>
    <!-- Dropdown menu -->
    <div id="dropdownHover3"
        class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
        <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownHoverButton3">
            <li>
                <a href="{{ url('/admin/audiodata') }}?hive_id={{$hive_id}}"
                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Audio</a>
            </li>
            <li>
                <a href="{{ url('/admin/videodata') }}?hive_id={{$hive_id}}"
                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Video</a>
            </li>
            <li>
                <a href="{{ url('/admin/photodata') }}?hive_id={{$hive_id}}"
                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Images</a>
            </li>
        </ul>
    </div>


    {{-- Hive Sensor monitoring --}}
    <button id="dropdownHoverButton4" data-dropdown-toggle="dropdownHover4" data-dropdown-trigger="hover"
        class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-4 py-2.5 text-center inline-flex items-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800"
        type="button">
            Hive Monitoring
        <svg class="w-4 h-4 ml-2" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg></button>
    <!-- Dropdown menu -->
    <div id="dropdownHover4"
        class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
        <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownHoverButton3">
            <li>
                <a href="/sensor-monitoring"
                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Sensor Monitoring</a>
            </li>

            @if(isset($hive) && isset($hive->id))
                <a href="{{ route('thingspeak.monitoring', ['hive_id' => $hive->id]) }}"
                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Battery Monitoring</a>
            @else
                <a href="{{ route('thingspeak.monitoring', ['hive_id' => $hive_id ?? null]) }}"
                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Battery Monitoring</a>
            @endif

        </ul>
    </div>

    {{-- Analytics Dropdown --}}
    <button id="dropdownHoverButton5" data-dropdown-toggle="dropdownHover5" data-dropdown-trigger="hover"
        class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-4 py-2.5 text-center inline-flex items-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800"
        type="button">
        Analytics
        <svg class="w-4 h-4 ml-2" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>
    <!-- Dropdown menu -->
    <div id="dropdownHover5"
        class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
        <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownHoverButton5">
            <li>
                <a href="{{url('analytics/weight_analytics')}}"
                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">weight analytics</a>
            </li>
            <li>
                <a href="{{url('analytics/temperature_humidity')}}"
                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Temperature vs humidity</a>
            </li>
            <li>
                <a href=""
                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Trends</a>
            </li>
        </ul>
    </div>


    {{-- Data Analysis Button --}}
    <div>
    <a href="{{url('analytics/data_reports')}}" 
           class="inline-block px-4 py-2.5 text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm">
            Data Reports
        </a>
    </div>



</div>
