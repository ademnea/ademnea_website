@extends('layouts.app')
@section('content')

<div id="myTableContainer" class="relative p-3 mt-4 overflow-x-auto shadow-md sm:rounded-lg">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-2">
            <input type="text" id="searchInput" placeholder="Search users..." class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white w-64">
            <select id="filterRole" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="">All Roles</option>
                <option value="admin">Admin</option>
                <option value="user">User</option>
            </select>
            <button id="resetFilters" class="text-white bg-gray-500 hover:bg-gray-600 focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-4 py-2.5">Reset</button>
        </div>
        <div class="flex items-center space-x-2">
            <label class="text-sm text-gray-700 dark:text-gray-400">Show</label>
            <select id="pageLengthSelect" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
            <label class="text-sm text-gray-700 dark:text-gray-400">entries</label>
        </div>
    </div>
    <table id="myTable" class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">#</th>
                <th scope="col" class="px-6 py-3">Name</th>
                <th scope="col" class="px-6 py-3">Email</th>
                <th scope="col" class="px-6 py-3">Created</th>
                <th scope="col" class="px-6 py-3">Updated</th>
                <th scope="col" class="px-6 py-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $loop->iteration }}</td>
                <td class="flex items-center px-6 py-4 text-gray-900 whitespace-nowrap dark:text-white">
                    <div class="pl-3">
                        <div class="text-base font-semibold">{{ $user->name }}</div>
                    </div>
                </td>
                <td class="px-6 py-4">{{ $user->email }}</td>
                <td class="px-6 py-4">{{ $user->created_at->format('M d, Y') }}</td>
                <td class="px-6 py-4">{{ $user->updated_at->format('M d, Y') }}</td>
                <td class="px-6 py-4">
                    <div class="flex space-x-2">
                        <a href="#" data-modal-target="edit-{{ $user->id }}" data-modal-show="edit-{{ $user->id }}" style="color: white; background-color: #ffc107; padding: 6px 12px; border-radius: 5px; font-size: 0.75rem; font-weight: 600;" class="inline-flex items-center">Edit</a>
                        <a href="#" data-modal-target="delete-{{ $user->id }}" data-modal-show="delete-{{ $user->id }}" style="color: white; background-color: #dc3545; padding: 6px 12px; border-radius: 5px; font-size: 0.75rem; font-weight: 600;" class="inline-flex items-center">Delete</a>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Edit Modals -->
@foreach($users as $user)
<div id="edit-{{ $user->id }}" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 items-center justify-center hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative w-full max-w-2xl max-h-full">
        <form action="{{ url('/users/' . $user->id) }}" method="POST" class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            {{ method_field('PUT') }}
            {{ csrf_field() }}
            <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Edit User</h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="edit-{{ $user->id }}">
                    <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </button>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-6 gap-6">
                    <div class="col-span-6 sm:col-span-3">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name</label>
                        <input name="name" type="text" value="{{ old('name', $user->name) }}" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-600 focus:border-green-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" required>
                    </div>
                    <div class="col-span-6 sm:col-span-3">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                        <input name="email" type="email" value="{{ old('email', $user->email) }}" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-600 focus:border-green-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" required>
                    </div>
                    <div class="col-span-6 sm:col-span-3">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">New Password</label>
                        <input name="password" type="password" placeholder="Leave blank to keep current" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-600 focus:border-green-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                    </div>
                    <div class="col-span-6 sm:col-span-3">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Confirm Password</label>
                        <input name="password_confirmation" type="password" placeholder="Confirm new password" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-600 focus:border-green-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                    </div>
                </div>
            </div>
            <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button type="submit" class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endforeach

<!-- Delete Modals -->
@foreach($users as $user)
<div id="delete-{{ $user->id }}" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <button type="button" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-800 dark:hover:text-white" data-modal-hide="delete-{{ $user->id }}">
                <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </button>
            <div class="p-6 text-center">
                <svg aria-hidden="true" class="mx-auto mb-4 text-gray-400 w-14 h-14 dark:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to delete <strong>{{ $user->name }}</strong>?</h3>
                <form method="POST" action="{{ url('/users/' . $user->id) }}" style="display:inline">
                    {{ method_field('DELETE') }}
                    {{ csrf_field() }}
                    <button type="submit" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                        Yes, delete
                    </button>
                </form>
                <button data-modal-hide="delete-{{ $user->id }}" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection

@section('page_scripts')
<script>
$(document).ready(function() {
    var table = $('#myTable').DataTable({
        responsive: true,
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50]
    });

    $('#searchInput').on('keyup', function() {
        table.search($(this).val()).draw();
    });

    $('#filterRole').on('change', function() {
        table.search($(this).val()).draw();
    });

    $('#resetFilters').on('click', function() {
        $('#searchInput').val('');
        $('#filterRole').val('');
        table.search('').draw();
    });

    $('#pageLengthSelect').on('change', function() {
        table.page.len($(this).val()).draw();
    });
});

$(document).on('click', '[data-modal-show]', function() {
    $('#' + $(this).data('modal-show')).removeClass('hidden').addClass('flex');
});

$(document).on('click', '[data-modal-hide]', function() {
    $('#' + $(this).data('modal-hide')).addClass('hidden').removeClass('flex');
});
</script>
@endsection
