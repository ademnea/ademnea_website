@extends('layouts.app')
@section('content')

<style>
    .pagination {
        display: flex;
        justify-content: center;
        margin-top: 1rem;
    }
    
    .pagination li {
        margin: 0 0.25rem;
    }
    
    .pagination a, .pagination span {
        display: inline-block;
        padding: 0.5rem 1rem;
        border: 1px solid #ddd;
        border-radius: 0.25rem;
    }
    
    .pagination a:hover {
        background-color: #eee;
    }
    
    .pagination .active span {
        background-color: #4f46e5;
        color: white;
        border-color: #4f46e5;
    }
    
    .pagination .disabled span {
        color: #6b7280;
        background-color: #f3f4f6;
        border-color: #e5e7eb;
    }
</style>


<div class="relative p-3 mt-2 overflow-x-auto shadow-md sm:rounded-lg">
    <button type="button" data-modal-target="addwork" data-modal-show="addwork" class="text-white ml-4 mt-4 bg-green-700 hover:bg-green-800 focus:outline-none focus:ring-4 focus:ring-green-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Add New Newsletter</button>

    
    
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">#</th>
                <th scope="col" class="px-6 py-3">Title</th>
                <th scope="col" class="px-6 py-3">Image</th>
                <th scope="col" class="px-6 py-3">Description</th>
                <th scope="col" class="px-6 py-3" >Article</th>
                <th scope="col" class="px-6 py-3">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($newsletter as $item)
            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                <td class="px-6 py-4">
                    {{ ($newsletter->currentPage() - 1) * $newsletter->perPage() + $loop->iteration }}
                </td>
                <td class="px-6 py-4">{{ $item->title }}</td>
                <td class="px-6 py-4">
                    @if($item->image)
                        <img src="{{ asset($item->image) }}" alt="{{ $item->title }}" style="width: 90px; height: 90px; object-fit: cover; border-radius: 10px;">
                    @else
                        <span class="text-gray-400">No image</span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <p>{{ Str::words(strip_tags($item->description), 10, '...') }}</p>
                </td>
                <td class="px-6 py-4" style="width: 40%;">
                    <details>
                        <summary>View article</summary>
                        <p>{!! $item->article !!}</p>
                    </details>
                </td>
               <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex space-x-2">
                        <a href="#" type="button" data-modal-target="view-{{ $item->id }}" data-modal-show="view-{{ $item->id }}" style="color: white; background-color: #28a745; padding: 6px 12px; border-radius: 5px; font-size: 0.75rem; font-weight: 600;" class="inline-flex items-center">View</a>
                        <a href="#" data-modal-target="{{ $item->id }}" data-modal-show="{{ $item->id }}" style="color: white; background-color: #ffc107; padding: 6px 12px; border-radius: 5px; font-size: 0.75rem; font-weight: 600;" class="inline-flex items-center">Edit</a>
                        <a href="#" type="button" data-modal-target="delete-{{ $item->id }}" data-modal-show="delete-{{ $item->id }}" style="color: white; background-color: #dc3545; padding: 6px 12px; border-radius: 5px; font-size: 0.75rem; font-weight: 600;" class="inline-flex items-center">Delete</a>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <!-- Laravel Pagination Links -->
    <div class="mt-4">
        {{ $newsletter->links() }}
    </div>
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
                    <div class="col-span-6 sm:col-span-6">
                        <label for="image-add" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Image</label>
                        <input type="file" name="image" id="image-add" accept="image/*" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:text-gray-400 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400">
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
                       <div class="col-span-6 sm:col-span-6">
                           <label for="image-{{ $item->id }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Image</label>
                           <input type="file" name="image" id="image-{{ $item->id }}" accept="image/*" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:text-gray-400 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400">
                           @if(!empty($item->image))
                               <div class="mt-3">
                                   <img src="{{ asset($item->image) }}" alt="{{ $item->title }}" style="max-width: 180px; height: auto; border-radius: 10px;">
                               </div>
                           @endif
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
        <div id="view-{{ $item->id }}" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative w-full max-w-4xl max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-gray-600">
                        <h3 class="text-xl font-medium text-gray-900 dark:text-white">
                        News Letter
                    </h3>
                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="view-{{ $item->id }}">
                            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                     <!-- Modal body -->
                     <div class="p-6 space-y-6">
                        <div class="space-y-4">
                            <h4 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $item->title }}</h4>

                            @if(!empty($item->image))
                                <div>
                                    <img src="{{ asset($item->image) }}" alt="{{ $item->title }}" class="w-full max-h-[420px] object-cover rounded-xl border border-gray-200 dark:border-gray-600">
                                </div>
                            @endif

                            <div class="prose max-w-none dark:prose-invert">
                                {!! $item->article !!}
                            </div>
                        </div>
                    <!-- Modal footer -->
                    <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                     <div class="flex justify-center">
                         <button type="submit" id="back-button" onclick="window.location.href = '/admin/newsletter'" data-modal-hide="view-{{ $item->id }}" class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-greeb-700 dark:focus:ring-green-800">
                             Back
                         </button>
                     </div>
                 </div>
                  </div>
                </div>
            </div>
        </div>
        @endforeach
    
 @foreach($newsletter as $item)
         <!-- Delete user -->
    <!-- adding deletig functionality-->
<!-- Add jQuery library -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<div id="delete-{{ $item->id }}" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
<div class="relative w-full max-w-md max-h-full">
    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
        <button type="button" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-800 dark:hover:text-white" data-modal-hide="delete-{{ $item->id }}">
            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            <span class="sr-only">Close modal</span>
        </button>
        <div class="p-6 text-center">
            <svg aria-hidden="true" class="mx-auto mb-4 text-gray-400 w-14 h-14 dark:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to delete this newsletter?</h3>
           <form method="POST" action="{{ url('admin/newsletter' . '/' . $item->id) }}" accept-charset="UTF-8" style="display:inline">
            {{ method_field('DELETE') }}
            {{ csrf_field() }}
            <button data-modal-hide="delete-{{ $item->id }}" type="submit" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                Yes, I'm sure
            </button>
           </form>
        
            <button data-modal-hide="delete-{{ $item->id }}" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">No, cancel</button>
        </div>
    </div>
</div>
</div>
 @endforeach
          
 </div>
 @endsection
 
 