@extends('layouts.app')
@section('content')

 @php
  $hive_id = session('hive_id');
@endphp

@include('datanavbar')


  <div class="container mx-auto flex">
    <div class="w-2/3">
        <div class="play-video">
            <video id="mainVideo" class="w-full" controls>
                
                <source src="{{ URL("hivevideo/" . $videos[0]->path) }}" type="video/mp4">
                    
            </video>
        </div>
    </div>
    <div class="w-1/3 ml-4">
        <div class="right-sidebar">
            <h2 class="text-lg font-semibold mb-4">Videos From The Hive</h2>
            
            <ul>
                @foreach($videos as $video)
                <li class="flex items-center space-x-4 py-2">
                    <div class="flex-shrink-0">
                        <a href="#">
                            <img src="{{ asset('thumbnail/thumbnail.jpeg') }}" data-video="{{ $video->path }}" alt="Video Thumbnail" class="w-16 h-12">
                        </a>
                    </div>
                    <div>
                        <a href="#" class="block hover:bg-gray-200 px-2 py-1 rounded playlist-item" data-video="{{ $video->path }}">
                            {{ $video->created_at }}
                        </a>
                        <p class="text-sm text-gray-600">
                            Bee Count: {{ optional($video->latestBeeCount)->bee_count ?? 0 }}
                        </p>
                    </div>
                </li>
                @endforeach
            </ul>
            {{ $videos->appends(['hive_id' => $hive_id])->links() }}

            
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log("DOM content loaded.");
        
        const playlistItems = document.querySelectorAll('.playlist-item');

        playlistItems.forEach(function(item) {
            item.addEventListener('click', function(event) {
                event.preventDefault();
                const videoSource = this.getAttribute('data-video');
                console.log(videoSource);
                document.getElementById('mainVideo').src = "{{ URL('hivevideo') }}/" + videoSource;
                document.getElementById('mainVideo').load();
            });
        });
    });
</script>

@endsection

@section('page_scripts')

@endsection


