@extends('layouts.app')
@section('content')


<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
<button type="button" data-modal-target="addwork" data-modal-show="addwork" class="text-white ml-4 mt-4 bg-green-700 hover:bg-green-800 focus:outline-none focus:ring-4 focus:ring-green-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Add New Newsletter</button>
    <table  id="myTable" class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">
                    #
                </th>
                <th scope="col" class="px-6 py-3">
                    Title
                </th>
                <th scope="col" class="px-6 py-3">
                    Article
                </th>
                <th scope="col" class="px-6 py-3">
                    Description
                </th>
            </tr>
        </thead>
        <tbody>
        @foreach($newsletter as $item)
            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                {{ $item->id }}
                </th>
                <td class="px-6 py-4">
                {{ $item->title }}
                </td>
                <td class="px-6 py-4">
                {{ $item->article }}
                </td>
                <td class="px-6 py-4">        
                
                    {{ $item->description }}
        
                </td>
                <td class="px-6 py-4">
                    <a href="#" type="button" data-modal-target="{{ $item->name }}" data-modal-toggle="{{ $item->name }}" class="font-medium text-green-600 dark:text-green-500 hover:underline">View</a>
                    <a href="#" data-modal-target="{{ $item->id }}" data-modal-show="{{ $item->id }}" class="font-medium text-green-600 dark:text-green-500 hover:underline">Edit</a>
                    <a href="#" type="button" data-modal-target="{{ $item->description }}" data-modal-toggle="{{ $item->description}}" class="font-medium text-red-600 dark:text-blue-500 hover:underline">Delete</a>

                </td>
            </tr>
            @endforeach 
        </tbody>
    </table>
</div>
<!-- Add New Newsletter modal -->
<div id="addwork" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 items-center justify-center hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative w-full max-w-2xl max-h-full">
        <!-- Modal content -->
        <form action="{{ url('/admin/newsletter') }}" method='POST'  accept-charset="UTF-8" enctype="multipart/form-data" method="POST" class="relative bg-white rounded-lg shadow dark:bg-gray-700">
        {{ csrf_field() }}
            <!-- Modal header -->
            <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Add New Newsletter
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="addwork">
                    <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>  
                </button>
            </div>
            <!-- Modal body -->
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-6 gap-6">
                    <div class="col-span-6 sm:col-span-6">
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Title</label>
                        <input type="text" name="title" id="title" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-600 focus:border-green-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500"  required="">
                    </div>
                    <div class="col-span-6 sm:col-span-6">
                        <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Article description</label>
                        <textarea class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-600 focus:border-green-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500"  rows="5" name="description" type="textarea" id="description" ></textarea>
                    </div>
                    <div class="col-span-6 sm:col-span-6">
                        <label for="article" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Article Details</label>
                        <textarea class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-600 focus:border-green-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500"  rows="5" name="article" type="textarea" id="article" ></textarea>
                    </div>

                    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
                     <script>
                         
                     CKEDITOR.replace('article', {
                             filebrowserUploadUrl: "{{route('upload', ['_token' => csrf_token() ])}}",
                             filebrowserUploadMethod: 'form',
                             "extraPlugins" : 'imagebrowser',
                             "imageBrowser_listUrl" : "/images_list.json"
                         })

                         CKEDITOR.replace('description', {
                             filebrowserUploadUrl: "{{route('upload', ['_token' => csrf_token() ])}}",
                             filebrowserUploadMethod: 'form',
                             "extraPlugins" : 'imagebrowser',
                             "imageBrowser_listUrl" : "/images_list.json"
                         })
                     </script>


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

<!-- Edit Newsletter modal -->
@foreach($newsletter as $item)
<div id="{{ $item->id }}" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 items-center justify-center hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
       <div class="relative w-full max-w-2xl max-h-full">
           <!-- Modal content -->
           <form action="{{ url('/admin/newsletter/' . $item->id) }}" method='POST'  accept-charset="UTF-8" enctype="multipart/form-data" method="POST" class="relative bg-white rounded-lg shadow dark:bg-gray-700">
           {{ method_field('PATCH') }}
           {{ csrf_field() }}
               <!-- Modal header -->
               <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                   <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                       Edit Newsletter
                   </h3>
                   <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="{{ $item->id }}">
                       <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>  
                   </button>
               </div>
               <!-- Modal body -->
               <div class="p-6 space-y-6">
                   <div class="grid grid-cols-6 gap-6">
                   <div class="col-span-6 sm:col-span-6">
                           <label for="title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Title</label>
                           <input type="text" value="{{ old('title', $item->title) }}" name="title" id="title" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-600 focus:border-green-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500"  required="">
                       </div>
                       <div class="col-span-6 sm:col-span-6">
                           <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Article description</label>
                           <textarea class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-600 focus:border-green-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500"  rows="5" name="description" type="textarea" id="description" >{{ old('description', $item->description) }}</textarea>
                       </div>
                       <div class="col-span-6 sm:col-span-6">
                           <label for="article" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Article Details</label>
                           <textarea class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-600 focus:border-green-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500"  rows="5" name="article" type="textarea" id="article" >{{ old('article', $item->article) }}</textarea>
                       </div>


                       <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
                        <script>
                            
                        CKEDITOR.replace('article', {
                                filebrowserUploadUrl: "{{route('upload', ['_token' => csrf_token() ])}}",
                                filebrowserUploadMethod: 'form',
                                "extraPlugins" : 'imagebrowser',
                                "imageBrowser_listUrl" : "/images_list.json"
                            })

                            CKEDITOR.replace('description', {
                                filebrowserUploadUrl: "{{route('upload', ['_token' => csrf_token() ])}}",
                                filebrowserUploadMethod: 'form',
                                "extraPlugins" : 'imagebrowser',
                                "imageBrowser_listUrl" : "/images_list.json"
                            })
                        </script>


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
        @foreach($newsletter as $item)
        <div id="{{ $item->name }}" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative w-full max-w-4xl max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-gray-600">
                        <h3 class="text-xl font-medium text-gray-900 dark:text-white">
                        DESCRIPTION
                    </h3>
                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="{{ $item->name }}">
                            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                     <!-- Modal body -->
                     <div class="p-6 space-y-6">
                        {{-- <div class="row"> <!--<h4 class="col-4">NAME</h4>--><h4 class="col-4">TITLE</h4></div> --}}
                        <div class="row"> <!--<h4 class="col-4">{{ $item->id }}</h4>--> <h4 class="col-4">{{ $item->name }} </h4><h4 class="col-4"> {{ $item->title }} </h4></div>
                        <hr> 
                        <p>
                           {{ $item->description }}
                           </p> 
                       </div>
                    <!-- Modal footer -->
                    <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                     <div class="flex justify-center">
                         <button type="submit" id="back-button" onclick="window.location.href = '/admin/newsletter'" data-modal-hide="{{ $item->name }}" class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-greeb-700 dark:focus:ring-green-800">
                             Back
                         </button>
                     </div>
                 </div>
                  </div>
                </div>
            </div>
        </div>
        @endforeach
     <!-- Delete user -->
     <!-- adding deletig functionality-->
 <!-- Add jQuery library -->
 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
 @foreach($newsletter as $item)
     <!-- Delete user -->
     <div id="{{ $item->description }}" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
     <div class="relative w-full max-w-md max-h-full">
         <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            
             <button type="button" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-800 dark:hover:text-white" data-modal-hide="{{ $item->description}}">
                 <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                 <span class="sr-only">Close modal</span>
             </button>
             <div class="p-6 text-center">
                 <svg aria-hidden="true" class="mx-auto mb-4 text-gray-400 w-14 h-14 dark:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                 <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to delete this Team member?</h3>
                 <!-- added id to the button Yes, I'm sure-->
                 <form id="delete-user-form-{{ $item->description }}" method="POST" action="{{ url('admin/newsletter' . '/' . $item->id) }}" accept-charset="UTF-8" style="display:inline">
                     {{ method_field('DELETE') }}
                     {{ csrf_field() }}
                     <button id="delete-user-btn" data-modal-hide="{{ $item->description }}"  type="submit" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                         Yes, I'm sure
                     </button>
                 </form>
                 <button id="cancel-button" onclick="window.location.href = '/admin/newsletter'" data-modal-hide="{{ $item->description }}" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                     No, cancel
                 </button>
                 
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
         // Redirect to the newsletter page
         window.location.href = "{{ url('admin/newsletter') }}";
     });
 
    // Event listener for back button click
 document.getElementById("back-button").addEventListener("click", function() {
    // Redirect to the newsletter page
    window.location.href = "{{ url('admin/newsletter') }}"; // Replace with the actual URL of the newsletter page
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
 
                 // Redirect to the newsletter page
                 window.location.href = "/admin/newsletter";
             }
         });
     });
 </script>
 @endsection
