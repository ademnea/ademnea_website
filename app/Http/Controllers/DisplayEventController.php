<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DisplayEventController extends Controller
{
    public function displayEvent(){

// this code sends events to the galleries page to be disaplayed.

$events = DB::table('events')
->leftJoin('event_photos', 'events.id', '=', 'event_photos.event_id')
->select('events.*', 'event_photos.photo_url')
->get()
->groupBy('id');



   return view('gallery',compact('events'));

    }

    // lets put a one that checks for the id received and sends this event to be viewed in fullscreen.


    public function view_gallery(Request $request){

       $id = $request->query('id');

       $names = DB::table('events')
       ->where('id',$id)
       ->select('*')
       ->get();

        $events = DB::table('event_photos')
        ->where('event_id',$id)
        ->select('*')
        ->get();

       // dd($events);

        return view('galleryview',compact('events','names'));

    }


}
