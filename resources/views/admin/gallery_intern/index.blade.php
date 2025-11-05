@extends('layouts.app')
@section('content')

<div class="relative p-3 mt-2 overflow-x-auto shadow-md sm:rounded-lg">
    <button type="button" data-modal-target="addevent" data-modal-show="addevent" class="text-white ml-4 mt-4 bg-green-700 hover:bg-green-800 focus:outline-none focus:ring-4 focus:ring-green-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
        Add New Gallery
    </button>

    <table id="myTable" class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">#</th>
                <th scope="col" class="px-6 py-3">Title</th>
                <th scope="col" class="px-6 py-3">Venue</th>
                <th scope="col" class="px-6 py-3">Date</th>
                <th scope="col" class="px-6 py-3">Description</th>
                <th scope="col" class="px-6 py-3">Photos</th>
                <th scope="col" class="px-6 py-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($galleries as $gallery)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ $loop->iteration }}
                    </th>
                    <td class="px-6 py-4">{{ $gallery->title }}</td>
                    <td class="px-6 py-4">{{ $gallery->venue }}</td>
                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($gallery->date)->format('F j, Y') }}</td>
                    <td class="px-6 py-4">{{ \Illuminate\Support\Str::limit($gallery->description, 50) }}</td>
                    <td class="px-6 py-4">{{ $gallery->photos->count() }} photos</td>
                    <td class="px-6 py-4">
                        <div class="flex space-x-2">
                            <a href="#" onclick="openViewModal({{ $gallery->id }}, `{{ addslashes($gallery->title) }}`, `{{ addslashes($gallery->venue) }}`, `{{ $gallery->date }}`, `{{ addslashes($gallery->description) }}`)" style="color: white; background-color: #28a745; padding: 8px; border-radius: 5px;" class="inline-flex items-center" title="View">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path></svg>
                            </a>
                            <button type="button" onclick="openEditModal({{ $gallery->id }}, `{{ addslashes($gallery->title) }}`, `{{ addslashes($gallery->venue) }}`, `{{ $gallery->date }}`, `{{ addslashes($gallery->description) }}`)" style="color: white; background-color: #ffc107; padding: 8px; border-radius: 5px; border: none;" class="inline-flex items-center" title="Edit">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path></svg>
                            </button>
                            <form method="POST" action="{{ route('gallery_interns.destroy', $gallery->id) }}" onsubmit="return confirm('Are you sure?');" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="color: white; background-color: #dc3545; padding: 8px; border-radius: 5px; border: none;" class="inline-flex items-center" title="Delete">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" clip-rule="evenodd"></path><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414L7.586 12l-1.293 1.293a1 1 0 101.414 1.414L9 13.414l2.293 2.293a1 1 0 001.414-1.414L11.414 12l1.293-1.293z" clip-rule="evenodd"></path></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No gallery entries found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>






<!-- Add New Gallery Modal -->
<div id="addevent" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 items-center justify-center hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative w-full max-w-2xl max-h-full">
        <form action="{{ route('gallery_interns.store') }}" method="POST" enctype="multipart/form-data" class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            @csrf
            <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Add New Gallery</h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="addevent">
                    <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </button>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-6 gap-6">
                    <div class="col-span-6 sm:col-span-3">
                        <label for="title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Title</label>
                        <input name="title" type="text" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-600 focus:border-green-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500" required>
                    </div>
                    <div class="col-span-6 sm:col-span-3">
                        <label for="venue" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Venue</label>
                        <input name="venue" type="text" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-600 focus:border-green-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500" required>
                    </div>
                    <div class="col-span-6 sm:col-span-3">
                        <label for="date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Date</label>
                        <input name="date" type="date" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-600 focus:border-green-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500" required>
                    </div>
                    <div class="col-span-6 sm:col-span-6">
                        <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Description</label>
                        <textarea name="description" rows="4" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-600 focus:border-green-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500"></textarea>
                    </div>
                    <div class="col-span-6 sm:col-span-6">
                        <label for="images" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Images</label>
                        <input type="file" name="images[]" multiple accept="image/*" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                    </div>
                </div>
            </div>
            <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button type="submit" class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Save all</button>
            </div>
        </form>
    </div>
</div>




<!-- Edit Modal -->
<div id="editevent" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 items-center justify-center hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative w-full max-w-2xl max-h-full">
        <form id="editForm" method="POST" enctype="multipart/form-data" class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            @csrf
            @method('PUT')
            <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Edit Gallery</h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="editevent">
                    <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </button>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-6 gap-6">
                    <div class="col-span-6 sm:col-span-3">
                        <label for="edit_title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Title</label>
                        <input type="text" name="title" id="edit_title" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-600 focus:border-green-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500">
                    </div>
                    <div class="col-span-6 sm:col-span-3">
                        <label for="edit_venue" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Venue</label>
                        <input type="text" name="venue" id="edit_venue" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-600 focus:border-green-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500">
                    </div>
                    <div class="col-span-6 sm:col-span-3">
                        <label for="edit_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Date</label>
                        <input type="date" name="date" id="edit_date" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-600 focus:border-green-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500">
                    </div>
                    <div class="col-span-6 sm:col-span-6">
                        <label for="edit_description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Description</label>
                        <textarea name="description" id="edit_description" rows="4" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-600 focus:border-green-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500"></textarea>
                    </div>
                    <div class="col-span-6 sm:col-span-6">
                        <label for="edit_photos" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Add New Photos (optional)</label>
                        <input type="file" name="images[]" id="edit_photos" multiple accept="image/*" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                    </div>
                </div>
            </div>
            <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button type="submit" class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Save all</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('page_scripts')
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

<script>
$(document).ready(function() {
    $('#myTable').DataTable({
        responsive: true
    });
});

// Modal functions
function openViewModal(id, title, venue, date, description) {
    // Implementation for view modal
    alert('View: ' + title);
}

function openEditModal(id, title, venue, date, description) {
    document.getElementById('edit_title').value = title;
    document.getElementById('edit_venue').value = venue;
    document.getElementById('edit_date').value = date;
    document.getElementById('edit_description').value = description;
    
    const form = document.getElementById('editForm');
    form.action = '/admin/gallery_intern/' + id;
    
    document.getElementById('editevent').classList.remove('hidden');
}

// Modal hide functionality
document.addEventListener('click', function(e) {
    if (e.target.matches('[data-modal-hide]')) {
        const modalId = e.target.getAttribute('data-modal-hide');
        document.getElementById(modalId).classList.add('hidden');
    }
    if (e.target.matches('[data-modal-show]')) {
        const modalId = e.target.getAttribute('data-modal-show');
        document.getElementById(modalId).classList.remove('hidden');
    }
});
</script>
@endsectionp class="text-xs text-gray-500 dark:text-gray-400 mt-1">You can upload multiple photos.</p>
          </div>
        </div>
      </div>

      <div class="flex items-center p-6 space-x-2 border-t dark:border-gray-600">
        <button type="submit" class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
          Save Changes
        </button>
        <button type="button" class="text-gray-700 dark:text-white hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg text-sm px-5 py-2.5" data-modal-hide="editevent">
          Cancel
        </button>
      </div>
    </form>
  </div>
</div>

</div>


<!-- View Gallery Modal -->
<div id="viewevent" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto h-[calc(100%-1rem)] max-h-full">
  <div class="relative w-full max-w-2xl max-h-full mx-auto">
    <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-6">
      <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">View Gallery</h3>
        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="viewevent" aria-label="Close">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
          </svg>
        </button>
      </div>

      <div class="p-6 space-y-6">
        <div class="grid grid-cols-6 gap-6">

          <div class="col-span-6 sm:col-span-3">
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Title</label>
            <p id="view_title" class="text-gray-700 dark:text-gray-300"></p>
          </div>

          <div class="col-span-6 sm:col-span-3">
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Venue</label>
            <p id="view_venue" class="text-gray-700 dark:text-gray-300"></p>
          </div>

          <div class="col-span-6 sm:col-span-3">
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Date</label>
            <p id="view_date" class="text-gray-700 dark:text-gray-300"></p>
          </div>

          <div class="col-span-6 sm:col-span-6">
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Description</label>
            <p id="view_description" class="text-gray-700 dark:text-gray-300 whitespace-pre-line"></p>
          </div>

        </div>
      </div>

      <div class="flex items-center p-6 space-x-2 border-t dark:border-gray-600">
        <button type="button" class="text-gray-700 dark:text-white hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg text-sm px-5 py-2.5" data-modal-hide="viewevent">
          Close
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Utility Styles (optional) -->
<style>
  .input-field {
    shadow-sm: true;
    background-color: #f9fafb;
    border: 1px solid #d1d5db;
    color: #111827;
    padding: 0.625rem;
    border-radius: 0.5rem;
    width: 100%;
  }
</style>

 
<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Your custom script -->
<script>
function openEditModal(id, title, venue, date, description) {
    document.getElementById('edit_title').value = title;
    document.getElementById('edit_venue').value = venue;
    document.getElementById('edit_date').value = date;
    document.getElementById('edit_description').value = description;

    const form = document.getElementById('editForm');
    const base = "{{ url('admin/gallery_interns') }}";
    form.action = `${base}/${id}`;

    document.getElementById('editevent').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

// Close modal on "Cancel" or "X" button
document.querySelectorAll('[data-modal-hide="editevent"]').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('editevent').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    });
});


function openViewModal(id, title, venue, date, description) {
    document.getElementById('view_title').textContent = title;
    document.getElementById('view_venue').textContent = venue;
    document.getElementById('view_date').textContent = date;
    document.getElementById('view_description').textContent = description;

    document.getElementById('viewevent').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

// Close modal on button click
document.querySelectorAll('[data-modal-hide="viewevent"]').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('viewevent').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    });
});
</script>


 @endsection
 