{{--<p>ALBUMS INDEX PAGE</p>--}}

{{--@if(count($albums)>0)--}}
{{--    <?php--}}
{{--    $colcount = count($albums);--}}
{{--    $i = 1;--}}
{{--    ?>--}}
{{--    <div id="albums">--}}
{{--        <div class="row text-center">--}}
{{--            @foreach($albums as $album)--}}
{{--                {{$album->name}}--}}
{{--                @if($i == $colcount)--}}
{{--                    <div class="medium-4 columns end">--}}
{{--                        <a href="/albums/{{$album->id}}">--}}
{{--                            <img class="thumbnail" src="/storage/album_covers/{{$album->cover_image}}"--}}
{{--                                 alt="{{$album->name}}">--}}
{{--                        </a>--}}
{{--                        <br>--}}
{{--                        <h4>{{$album->name}}</h4>--}}
{{--                        @else--}}
{{--                            <div class="medium-4 columns">--}}
{{--                                <a href="/albums/{{$album->id}}">--}}
{{--                                    <img class="thumbnail" src="/storage/album_covers/{{$album->cover_image}}"--}}
{{--                                         alt="{{$album->name}}">--}}
{{--                                </a>--}}
{{--                                <br>--}}
{{--                                <h4>{{$album->name}}</h4>--}}
{{--                                @endif--}}

{{--                                @if($i % 3 == 0)--}}
{{--                            </div>--}}
{{--                    </div>--}}
{{--                    <div class="row text-center">--}}
{{--                        @else--}}
{{--                    </div>--}}
{{--                @endif--}}
{{--                <?php--}}
{{--                $i++;--}}
{{--                ?>--}}

{{--            @endforeach--}}
{{--        </div>--}}
{{--    </div>--}}
{{--@else--}}
{{--    <p style="text-style:italicize">[Gallery will be Uploaded Soon!]</p>--}}
{{--@endif--}}






@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">Gallery Albums</div>
                    <div class="card-body">
                        <a href="{{ url('/admin/albums/create') }}" class="btn btn-success btn-sm" title="Add New Album">
                            <i class="fa fa-plus" aria-hidden="true"></i> Create New Album
                        </a>

                        <form method="GET" action="{{ url('/admin/albums') }}" accept-charset="UTF-8" class="form-inline my-2 my-lg-0 float-right" role="search">
                            <div class="input-group">
                                <input type="text" class="form-control" name="search" placeholder="Search..." value="{{ request('search') }}">
                                <span class="input-group-append">
                                    <button class="btn btn-secondary" type="submit">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </span>
                            </div>
                        </form>

                        <br/>
                        <br/>
                        <div class="table-responsive">
                            <table id="myTable"class="table">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Album Name</th>
                                    <th>Album Description</th>
                                    {{--                                        <th>Image</th>--}}
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($albums as $album)
                                    <tr>
                                        <td>{{ $loop->iteration }}{{'.'}}</td>
                                        <td><a href="{{ url('/admin/albums/' . $album->id) }}" style="text-transform:capitalize">{{$album->name}}</a></td>
                                        <td style="text-transform:capitalize">{{ $album->description }}</td>
{{--                                        <td><img src="{{asset('images/' . $album->cover_image)}}" alt=""></td>--}}

                                        <td>
                                            <a href="{{ url('/admin/albums/' . $album->id) }}" title="View Gallery"><button class="btn btn-info btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></button></a>
                                            <a href="{{ url('/admin/albums/' . $album->id . '/edit') }}" title="Edit Gallery"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button></a>

                                            <form method="POST" action="{{ url('/admin/albums' . '/' . $album->id) }}" accept-charset="UTF-8" style="display:inline">
                                                {{ method_field('DELETE') }}
                                                {{ csrf_field() }}
                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete Album" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                                            </form>

                                        </td>

                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
{{--                            <div class="pagination-wrapper"> {!! $albums->appends(['search' => Request::get('search')])->render() !!} </div>--}}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
