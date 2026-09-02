@extends('layouts.app')

@section('content')

@php
  $hive_id = session('hive_id');
@endphp

<link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>

@include('datanavbar')

<div class="container mx-auto px-4 relative">
    <div class="portfolio-area">
        <div class="container mx-auto px-10 md:px-5 lg:px-5 sm:px-5">
            <div class="row portfolio relative">
                @foreach($photos as $photo)
                <div class="col-12 col-sm-6 col-lg-3 single_gallery_item {{$photo->category}} mb-30 wow fadeInUp" data-wow-delay="{{$loop->index * 200}}ms">
                    <div class="single-portfolio-content relative">
                        <a href="{{ URL('hiveimage/'. $photo->path) }}" data-lightbox="hive-images" data-title="{{ $photo->name ?? 'Hive Image' }}" class="relative inline-block">
                            <img class="w-full transition-transform duration-500 transform hover:scale-125 " src="{{ URL('hiveimage/'. $photo->path) }}" alt="{{ $photo->name ?? 'Hive Image' }}">
                        </a>
                        {{-- <div>ID: {{ $photo->id }}</div> --}}
                        <div>Created at: {{ $photo->created_at }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            {{ $photos->appends(['hive_id' => $hive_id])->links('vendor.pagination.simple-custom') }}
        </div>
    </div>
</div>

<script src="{{ asset('js/lightbox-plus-jquery.js') }}"></script>

@endsection

@section('page_scripts')

@endsection 

