@extends('layouts.app')

<style>
    .button1{
        background-color: lightseagreen;
        color: white;
        height: 34px;
        width: 75px;
        border-radius: 15px;
        border-color: green;
        shadow: none;
        font-weight: bold;
    }

    .button2{
        background-color: mediumseagreen;
        color: white;
        height: 34px;
        width: 75px;
        border-radius: 15px;
        border-color: green;
        shadow: none;
        font-weight: bold;
    }

    .button3{
        background-color: seagreen;
        color: white;
        height: 34px;
        width: 85px;
        border-radius: 15px;
        border-color: green;
        shadow: none;
        font-weight: bold;
    }

    .button4{
        background-color: lightseagreen;
        color: white;
        height: 40px;
        width: 100px;
        border-radius: 5px;
        border-color: lightseagreen;
        shadow: none;
        font-weight: bold
    }
</style>
@section('content')
<div class="content-wrapper">
    <div class="row">
      <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">Newsletter {{ $newsletter->id }}</div>
                    <div class="card-body">

                        <a href="{{ url('/admin/newsletter') }}" title="Back"><button class="button2"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                        <a href="{{ url('/admin/newsletter/' . $newsletter->id . '/edit') }}" title="Edit Newsletter"><button class="button1"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>

                        <form method="POST" action="{{ url('admin/newsletter' . '/' . $newsletter->id) }}" accept-charset="UTF-8" style="display:inline">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                            <button type="submit" class="button3 bg-danger" title="Delete Newsletter" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                        </form>
                        <br/>
                        <br/>

                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>ID</th><td>{{ $newsletter->id }}</td>
                                    </tr>
                                    <tr><th> Title </th><td> {{ $newsletter->title }} </td></tr><tr><th> Description </th><td> {{ $newsletter->description }} </td></tr><tr><th> Image </th><td>
                                        @if($newsletter->image)
                                            <img src="{{ asset($newsletter->image) }}" alt="{{ $newsletter->title }}" style="max-width: 220px; height: auto;">
                                        @else
                                            No image
                                        @endif
                                    </td></tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
