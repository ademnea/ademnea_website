@extends('layouts.app')
@section('content')


<div class="relative p-3 mt-2 overflow-x-auto shadow-md sm:rounded-lg">
<button type="button" data-modal-target="addevent" data-modal-show="addevent" class="text-white ml-4 mt-4 bg-green-700 hover:bg-green-800 focus:outline-none focus:ring-4 focus:ring-green-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Add New event</button>
    <table id="myTable"class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">
                    #
                </th>
                <th scope="col" class="px-6 py-3">
                    Title
                </th>
                <th scope="col" class="px-6 py-3">
                    Venue
                </th>
                <th scope="col" class="px-6 py-3">
                    Date
                </th>
                <th scope="col" class="px-6 py-3">
                  Description
                </th>
                <th scope="col" class="px-6 py-3">
                    Actions
                </th>
            </tr>
        </thead>
        <tbody>
        @foreach($event as $item)
            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                {{ $loop->iteration }}
                </th>
                <td class="px-6 py-4">
                {{ $item[0]->title }}
                </td>
                <td class="px-6 py-4">
                {{ $item[0]->venue }}
                </td>
                <td class="px-6 py-4">
                {{ $item[0]->date }}
                </td>
                <td class="px-6 py-4">
                {{ $item[0]->description }}
                </td>
                <td class="px-6 py-4">
                    <!-- Modal toggle -->
                    <div class="flex space-x-2">
                        <a href="#" type="button" data-modal-target="{{ $item[0]->title }}" data-modal-show="{{ $item[0]->title }}" style="color: white; background-color: #28a745; padding: 8px; border-radius: 5px;" class="inline-flex items-center" title="View">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path></svg>
                        </a>
                        <a href="#" type="button" data-modal-target="{{ $item[0]->id }}" data-modal-show="{{ $item[0]->id}}" style="color: white; background-color: #ffc107; padding: 8px; border-radius: 5px;" class="inline-flex items-center" title="Edit">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path></svg>
                        </a>
                        <a href="#" type="button" data-modal-target="popup-modal" data-modal-show="popup-modal" style="color: white; background-color: #dc3545; padding: 8px; border-radius: 5px;" class="inline-flex items-center" title="Delete">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" clip-rule="evenodd"></path><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414L7.586 12l-1.293 1.293a1 1 0 101.414 1.414L9 13.414l2.293 2.293a1 1 0 001.414-1.414L11.414 12l1.293-1.293z" clip-rule="evenodd"></path></svg>
                        </a>
                    </div>
                                       
                </td>
                {{-- <td class="px-6 py-4">
                    <a href="#" data-modal-target="{{ $item[0]->id }}" data-modal-show="{{ $item[0]->id }}" style="color: white; background-color: #ffc107; width: auto; height: 30px; padding: 5px; border-radius: 5px;" class="">Edit</a>
                </td> --}}
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

   
 <!-- Add new farm modal -->
 <div id="addevent" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 items-center justify-center hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative w-full max-w-2xl max-h-full">
        <!-- Modal content -->
        <form action="{{ url('/post_events') }}" method="POST" class="relative bg-white rounded-lg shadow dark:bg-gray-700" accept-charset="UTF-8" enctype="multipart/form-data">
                         {{ csrf_field() }}

            <!-- Modal header -->
            <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Add New Event
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="addevent">
                    <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>  
                </button>
            </div>
            <!-- Modal body -->
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-6 gap-6">
                    <div class="col-span-6 sm:col-span-3">
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ 'Title' }}</label>
                        <input  name="title" type="text" id="name"  class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-600 focus:border-green-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500" >
                    </div>
                    <div class="col-span-6 sm:col-span-3">
                        <label for="title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ 'Venue' }}</label>
                        <input  name="venue" type="text" id="title"  class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-600 focus:border-green-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500" >
                    </div>
                    <div class="col-span-6 sm:col-span-3">
                        <label for="publisher" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ 'Article Link' }}</label>
                        <input type="text"  name="article" id="publisher"  class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-600 focus:border-green-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500"  >
                    </div>
                    <div class="col-span-6 sm:col-span-3">
                        <label for="publisher" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ 'Date' }}</label>
                        <input type="date"  name="date" id="publisher"  class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-600 focus:border-green-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500"  >
                    </div>
                    
                    <div class="col-span-6 sm:col-span-3">
                        <label for="year" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ 'Description' }}</label>
                        <textarea class="form-control" name="description" placeholder="Event Description" id="floatingTextarea2" style="height: 100px"></textarea>         
                    </div>

                    <div class="col-span-6 sm:col-span-6">
                        <label for="attachment" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ 'Images' }}</label>
                         <input type="file" name="images[]" id="image" multiple required class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                         <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help"> (only images allowed)</p>
                    </div>
                   
                </div>
            </div>
            <!-- Modal footer -->
            <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button type="submit" class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Save all</button>
            </div>
        </form>
    </div>
</div>
</div>
 
 <!-- Edit farm modal -->
 @foreach($event as $item)
 <div id="{{ $item[0]->id}}" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 items-center justify-center hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
       <div class="relative w-full max-w-2xl max-h-full">
           <!-- Modal content -->
           <form action="/admin/event?id={{ $item[0]->id }}" method="POST" class="relative bg-white rounded-lg shadow dark:bg-gray-700" accept-charset="UTF-8" enctype="multipart/form-data">
             {{ csrf_field() }}

               <!-- Modal header -->
               <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                   <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                       Edit event
                   </h3>
                   <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="{{ $item[0]->id}}">
                       <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>  
                   </button>
               </div>
               <!-- Modal body -->
               <div class="p-6 space-y-6">
                   <div class="grid grid-cols-6 gap-6">
                       <div class="col-span-6 sm:col-span-3">
                           <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ 'Title' }}</label>
                           <input  name="title" value="{{ old('title', $item[0]->title) }}" type="text" id="name"  class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-600 focus:border-green-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500">
                       </div>
                       <div class="col-span-6 sm:col-span-3">
                           <label for="title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ 'Venue' }}</label>
                           <input  name="venue" value="{{ old('venue', $item[0]->venue) }}" type="text" id="title"  class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-600 focus:border-green-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500">
                       </div>
                       <div class="col-span-6 sm:col-span-3">
                           <label for="publisher" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ 'Date' }}</label>
                           <input type="date" value="{{ old('date', $item[0]->date) }}"  name="date" id="publisher"  class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-600 focus:border-green-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500">
                       </div>

                       <div class="col-span-6 sm:col-span-3">
                           <label for="year" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ 'Article Link' }}</label>
                           <input type="text" name="article" value="{{ old('article', $item[0]->article_link) }}"  class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-600 focus:border-green-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500">
                       </div>

                       {{-- lets put the description here --}}
                       <div class="col-span-6 sm:col-span-3">
                        <label for="year" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ 'Description' }}</label>
                        <textarea class="form-control" name="description" placeholder="Event Description" id="floatingTextarea2" style="height: 100px">
                            {{ old('description', $item[0]->description) }}
                        </textarea>         
                    </div>

                    {{-- lets first get ideas on how to edit the photos --}}

                       {{-- <div class="col-span-6 sm:col-span-6">
                        <label for="attachment" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ 'Image' }}</label>
                         <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"  name="image" id="attachment" type="file">
                         <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help"> (only image allowed)</p>
                       </div>
                       --}}
        
                   </div>
               </div>
               <!-- Modal footer -->
               <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                   <button type="submit" class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Save all</button>
               </div>
           </form>
       </div>
   </div>
</div>
@endforeach
 
     <!-- Large Modal -->
        <!-- Large Modal -->
        @foreach($event as $item)
        <div id="{{ $item[0]->title }}" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative w-full max-w-4xl max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-gray-600">
                        <h3 class="text-xl font-medium text-gray-900 dark:text-white">
                        event
                    </h3>
                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="{{ $item[0]->title }}">
                            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                     <!-- Modal body -->
                     <div class="p-6 space-y-6">
                        {{-- <div class="row"> <!--<h4 class="col-4">NAME</h4>--><h4 class="col-4">TITLE</h4></div> --}}
                        <div class="row"> <!--<h4 class="col-4">{{ $item[0]->id }}</h4>--> <h4 class="col-4">{{ $item[0]->title }} </h4>
                        <p>
                         <br>
                         {{ $item[0]->description}}
                           </p> 
                       </div>
                    <!-- Modal footer -->
                    <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                     <div class="flex justify-center">
                         <button type="submit" id="back-button" onclick="window.location.href = '/admin/event'" data-modal-hide="{{ $item[0]->title}}" class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-greeb-700 dark:focus:ring-green-800">
                             Back
                         </button>
                     </div>
                 </div>
                  </div>
                </div>
            </div>
        </div>
        @endforeach
     
 
 @foreach($event as $item)
          <!-- Delete user -->
    <!-- adding deletig functionality-->
<!-- Add jQuery library -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<div id="popup-modal" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
<div class="relative w-full max-w-md max-h-full">
    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
        <button type="button" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-800 dark:hover:text-white" data-modal-hide="popup-modal">
            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            <span class="sr-only">Close modal</span>
        </button>
        <div class="p-6 text-center">
            <svg aria-hidden="true" class="mx-auto mb-4 text-gray-400 w-14 h-14 dark:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to delete this Team member?</h3>
           <!-- added id to the button Yes, I'm sure-->
           <form id="delete-user-form-{{ $item[0]->id}}" method="POST" action="{{ url('admin/gallery#' . '/' . $item[0]->id) }}" accept-charset="UTF-8" style="display:inline">
            {{ method_field('DELETE') }}
            {{ csrf_field() }}
            <button id="delete-user-btn" data-modal-hide="popup-modal" type="submit" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                Yes, I'm sure
            </button>
           </form>
           
            <button data-modal-hide="popup-modal" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">No, cancel</button>
        </div>
    </div>
</div>
</div>
 @endforeach
          
 </div>
 @endsection
 
 
 <!-- added pagination and search-->
 @section('page_scripts')
 <!-- Include DataTables JS file -->
 <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
 <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
 
 <script>
   $(document).ready(function() {
    $('#myTable').DataTable({
       responsive: true
    });
 });
 
       // Event listener for delete button
       $(".delete-user-btn").on("click", function() {
         var confirmDelete = confirm("Confirm delete?");
         if (confirmDelete) {
             var form = $(this).closest('form');
             form.submit();
         }
     });
 
  // Event listener for back button click
  $(".back-button").on("click", function() {
         // Redirect to the team page
         window.location.href = "{{ url('admin/event') }}";
     });
 
    // Event listener for back button click
 document.getElementById("back-button").addEventListener("click", function() {
    // Redirect to the team page
    window.location.href = "{{ url('admin/event') }}"; // Replace with the actual URL of the team page
 });
   // Handle form submission
   $('#addTeamForm').on('submit', function(event) {
         event.preventDefault(); // Prevent the form from submitting normally
 
         // Perform an AJAX request to save the data
         $.ajax({
             url: $(this).attr('action'), // Form action URL
             method: $(this).attr('method'), // Form method (e.g., POST)
             data: new FormData(this), // Form data
             processData: false,
             contentType: false,
             success: function(response) {
                 // Handle the successful response
                 // Update the table with the new row
                 var table = $('#myTable').DataTable(); // Use the updated ID for the table
                 table.row.add([
                     // Add the data to the new row in the table
                     response.name,
                     response.position,
                     response.description,
                     '<a href="#" type="button" data-modal-target="'+ response.id + '" data-modal-toggle="'+ response.id + '" class="font-medium text-green-600 dark:text-green-500 hover:underline">View</a>' +
                     ' | <a href="#" type="button" data-modal-target="'+ response.id + '" data-modal-show="'+ response.id + '" class="font-medium text-green-600 dark:text-green-500 hover:underline">Edit</a>' +
                     ' | <a href="#" type="button" data-modal-target="'+ response.id + '" data-modal-toggle="'+ response.id + '" class="font-medium text-red-600 dark:text-blue-500 hover:underline">Delete</a>'
                 ]).draw(false);
 
                 // Reset the form
                 $('#addTeamForm')[0].reset();
 
                 // Close the modal or perform any other necessary actions
                 // ...
 
                 // Display a success message
                 $('#addTeamForm').append('<div class="text-green-600">Successfully added a new member.</div>');
 
                 // Remove the success message after a few seconds
                 setTimeout(function() {
                     $('.text-green-600').remove();
                 }, 3000);
 
                 // Redirect to the event page
                 window.location.href = "/admin/event";
             }
         });
     });
 </script>
 @endsection
 