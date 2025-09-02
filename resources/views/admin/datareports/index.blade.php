@extends('layouts.app')
@section('content')

<div class="relative bg-white p-6 rounded-lg shadow-md">

    {{-- Include Data NavBar --}}
    @include('datanavbar')

    <!-- Instructions -->
    <div class="bg-blue-100 border border-blue-300 text-blue-800 text-sm rounded-md px-4 py-3 mb-4">
        <p><strong>Instructions:</strong> Select the <span class="font-semibold">start month</span>, <span class="font-semibold">end month</span>, <span class="font-semibold">year</span>, and the <span class="font-semibold">attributes</span> you want in the report. Then click <span class="italic font-semibold">"Generate Report"</span>.</p>
        <p class="mt-1 text-xs text-gray-600">Note: If data is not available for the selected period or attribute, an empty report or a message will be shown.</p>
    </div>

    <!-- Hive Heading -->
    <h3 class='mx-2 font-bold py-1 text-green-600'>
        Hive : <span class="font-extrabold">{{ $hive_id ?? '' }}</span>
    </h3>

    <!-- Month/Year Selection -->
    <h3 class='mx-2 font-bold py-1 text-green-600'>Select Date Range & Attributes</h3>

    <div class="flex items-center space-x-4 mb-4">
        <!-- Start Month -->
        <div class="relative w-32">
            <select id="start_month" class="block appearance-none w-full bg-white border-2 border-green-800 rounded-lg py-2 pl-3 pr-3 text-gray-700 leading-tight focus:outline-none focus:border-green-500">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}">{{ $m }}</option>
                @endfor
            </select>
        </div>

        <!-- End Month -->
        <div class="relative w-32">
            <select id="end_month" class="block appearance-none w-full bg-white border-2 border-green-800 rounded-lg py-2 pl-3 pr-3 text-gray-700 leading-tight focus:outline-none focus:border-green-500">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}">{{ $m }}</option>
                @endfor
            </select>
        </div>

        <!-- Year -->
        <div class="relative w-32">
            <select id="year" class="block appearance-none w-full bg-white border-2 border-green-800 rounded-lg py-2 pl-3 pr-3 text-gray-700 leading-tight focus:outline-none focus:border-green-500">
                @foreach ([2022, 2023, 2024, 2025] as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Attributes Selection -->
    <div class="flex flex-wrap gap-4 mb-4">
        @foreach(['co2' => 'Carbon Dioxide', 'temperature' => 'Temperature', 'humidity' => 'Humidity', 'weight' => 'Weight'] as $key => $label)
            <label class="flex items-center space-x-2">
                <input type="checkbox" name="attributes[]" value="{{ $key }}" class="form-checkbox h-4 w-4 text-green-600">
                <span>{{ $label }}</span>
            </label>
        @endforeach
    </div>

    <!-- Generate Report Button -->
    <button id="downloadBtn" class="bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors duration-300 text-center flex items-center justify-center space-x-1">
        <span class="mr-1">Generate Report</span>
        <svg class="w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m7-7l-7 7-7-7"></path>
        </svg>
    </button>

</div>

@endsection

@section('scripts')
<script>
document.getElementById('downloadBtn').addEventListener('click', function () {
    const startMonth = document.getElementById('start_month').value;
    const endMonth = document.getElementById('end_month').value;
    const year = document.getElementById('year').value;
    const hiveId = "{{ $hive_id ?? '' }}";

    // Collect selected attributes
    const attributes = Array.from(document.querySelectorAll('input[name="attributes[]"]:checked')).map(cb => cb.value);

    if(attributes.length === 0) {
        alert('Please select at least one attribute.');
        return;
    }

    const originalHtml = this.innerHTML;
    this.innerHTML = '<span>Generating Report...</span>';
    this.disabled = true;

    fetch('/generate-local-report', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            start_month: startMonth,
            end_month: endMonth,
            year: year,
            hive_id: hiveId,
            attributes: attributes
        })
    })
    .then(response => {
        if (!response.ok) throw new Error('Failed to generate report');
        return response.json();
    })
    .then(data => {
        if (data.success && data.download_url) {
            const a = document.createElement('a');
            a.href = data.download_url;
            a.download = `hive_report_${hiveId}_${year}_${startMonth}-${endMonth}.zip`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        } else {
            throw new Error(data.message || 'Unknown error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error: ' + error.message);
    })
    .finally(() => {
        this.innerHTML = originalHtml;
        this.disabled = false;
    });
});
</script>
@endsection
