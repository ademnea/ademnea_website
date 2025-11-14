<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use App\Models\Gallery;
use App\Models\EventPhoto;
use App\Models\Event;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index(){

        $event = DB::table('events')
            ->join('event_photos', 'events.id', '=', 'event_photos.event_id')
            ->select('events.*', 'event_photos.photo_url')
            ->get()
            ->groupBy('id');

        return view('admin.gallery.index',compact('event'));
    }

    public function insert(Request $request){

       // return $request->input();
       //validate the incoming request
        $validatedData = $request->validate([
        'title' => 'required|string',
        'date' => 'required|date',
        'description' => 'required|string',
        'venue' => 'required|string',
        'article_link' => 'nullable|url',
        'images.*' => 'required|image|max:102400', // Maximum size is 100 MB (102400 KB)
    ]);
    
       DB::table('events')->insert([

        'title' => $request->title,
        'date' => $request->date,
        'description' => $request->description,
        'venue' => $request->venue,
        'article_link' => $request->article,
        'created_at' => now(),
        'updated_at' => now(),
          
    ]);

    //lets get the latest event and add this image to it.
    $latestRecord = Event::latest()->first();
    $latestRecordId = $latestRecord->id;

    //now lets insert the images in the event images table.
  
    if ($request->hasFile('images')) {
    foreach ($request->images as $file) {

       $picname = $file->getClientOriginalName();
       $file->move(public_path('images/events'), $picname);

        $fileModel = new EventPhoto;
        $fileModel->photo_url = $picname;
        $fileModel->event_id = $latestRecordId;
        $fileModel->save();          
             
}
}


    return redirect('admin/gallery/')->with('success', 'event added successfully!');

    }


    // function to edit the gallery events
    public function update(Request $request){

      //  return $request->input();
  
        $id = $request->input('id');

        //dd($id);

        DB::table('events')
        ->where('id', $id)
        ->update([
    
            'title' => $request->title,
            'venue' => $request->venue,
            'date' => $request->date,
            'description' => $request->description,
            'article_link' => $request->article,
            'updated_at' => now(),
        ]);
    
    // editing the phone pictures.
    
    if ($request->hasFile('images')) {
        // Step 1: Retrieve the existing photos
        $existingPhotos = EventPhoto::where('event_id', $id)->get();
    
        // Step 2: Delete the existing photos
        foreach ($existingPhotos as $existingPhoto) {
    
            // Delete the existing photo file from the server
            unlink(public_path('images/events/'.$existingPhoto->photo_url));
    
            // Delete the record from the database
            $existingPhoto->delete();
        }
    
        // Step 3: Add new photos
        foreach ($request->images as $file) {
            // Upload the image as you did before
            $picname = $file->getClientOriginalName();
            $file->move(public_path('images/events'), $picname);
    
            // Create a new EventPhoto model instance
            $fileModel = new EventPhoto;
            $fileModel->event_id = $id;
            $fileModel->photo_url = $picname;
            $fileModel->save();
        }
    }
    
    
    return redirect('admin/gallery/')->with('success', 'event updated successfully!');
    

    }
}
