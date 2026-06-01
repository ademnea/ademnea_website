@extends('layouts.app')
@section('content')

<div id="myTableContainer" class="relative p-3 mt-4 overflow-x-auto shadow-md sm:rounded-lg">
    <div class="mb-4">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">User Feedback</h2>
        <p class="text-gray-600 dark:text-gray-400">Messages from website contact form</p>
    </div>
    
    <table id="myTable" class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">Name</th>
                <th scope="col" class="px-6 py-3">Email</th>
                <th scope="col" class="px-6 py-3">Subject</th>
                <th scope="col" class="px-6 py-3">Date</th>
                <th scope="col" class="px-6 py-3">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($feedback as $item)
            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $item->name }}
                </td>
                <td class="px-6 py-4">{{ $item->email }}</td>
                <td class="px-6 py-4">{{ $item->subject }}</td>
                <td class="px-6 py-4">{{ $item->created_at->format('M d, Y') }}</td>
                <td class="px-6 py-4">
                    <div class="flex space-x-2">
                        <a href="#" type="button" data-modal-target="view-{{ $item->id }}" data-modal-show="view-{{ $item->id }}" style="color: white; background-color: #28a745; padding: 6px 12px; border-radius: 5px; font-size: 0.75rem; font-weight: 600;" class="inline-flex items-center">View</a>
                        <a href="#" type="button" data-modal-target="delete-{{ $item->id }}" data-modal-show="delete-{{ $item->id }}" style="color: white; background-color: #dc3545; padding: 6px 12px; border-radius: 5px; font-size: 0.75rem; font-weight: 600;" class="inline-flex items-center">Delete</a>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- View Modals -->
@foreach($feedback as $item)
<div id="view-{{ $item->id }}" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative w-full max-w-4xl max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-xl font-medium text-gray-900 dark:text-white">Feedback Details</h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="view-{{ $item->id }}">
                    <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </button>
            </div>
            <div class="p-6 space-y-6">
                <div><strong>Name:</strong> {{ $item->name }}</div>
                <div><strong>Email:</strong> {{ $item->email }}</div>
                <div><strong>Subject:</strong> {{ $item->subject }}</div>
                <div><strong>Date:</strong> {{ $item->created_at->format('M d, Y H:i') }}</div>
                <div><strong>Message:</strong><br>{{ $item->message }}</div>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Delete Modals -->
@foreach($feedback as $item)
<div id="delete-{{ $item->id }}" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <button type="button" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-800 dark:hover:text-white" data-modal-hide="delete-{{ $item->id }}">
                <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </button>
            <div class="p-6 text-center">
                <svg aria-hidden="true" class="mx-auto mb-4 text-gray-400 w-14 h-14 dark:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to delete this feedback?</h3>
                <form method="POST" action="{{ url('admin/feedback' . '/' . $item->id) }}" style="display:inline">
                    {{ method_field('DELETE') }}
                    {{ csrf_field() }}
                    <button type="submit" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                        Yes, delete
                    </button>
                </form>
                <button data-modal-hide="delete-{{ $item->id }}" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection

@section('page_scripts')
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#myTable').DataTable({
        responsive: true
    });
});

$(document).on('click', '[data-modal-show]', function(e) {
    const targetId = $(this).data('modal-show');
    $('#' + targetId).removeClass('hidden');
});

$(document).on('click', '[data-modal-hide]', function(e) {
    const targetId = $(this).data('modal-hide');
    $('#' + targetId).addClass('hidden');
});
</script>
@endsection