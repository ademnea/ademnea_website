<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\Models\Photo;
use App\Models\Albums;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhotosController extends Controller
{
    //
    public function create($album_id)
    {
        return view('admin/photos.create')->with('album_id', $album_id);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'photo.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if($request->hasfile('photo')) {

            foreach ($request->file('photo') as $photo) {


                // Get filename with extension
                $filenameWithExt = $photo->getClientOriginalName();

                // Get just the filename
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);

                //Get size
                $size = $photo->getSize();

                //Get extension
                $extension = $photo->getClientOriginalExtension();

                // Create new filename
                $filenameToStore = $filename . '_' . time() . '.' . $extension;

                //Upload image
                $path = $photo->storeAs('public/photos/' . $request->input('album_id'), $filenameToStore);

                // Upload photo
                $photo = new Photo;
                $photo->album_id = $request->input('album_id');
                $photo->title = $request->input('title');
                $photo->description = $request->input('description');
                $photo->size = $size;
                $photo->type = $extension;
                $photo->photo = $filenameToStore;

                $photo->save();
            }
        }
        return redirect('/albums/'.$request->input('album_id'))->with('success', 'Photo successfully Uploaded!');
    }

    public function show($id){
        $photo = Photo::find($id);
        if(Auth::check()) {
            return view('admin/photos.show')->with('photo', $photo);
        }else{
            return view('website/photo_show')->with('photo', $photo);
        }
    }

    public function destroy($id){
        $photo = Photo::find($id);
        if(storage::delete('public/photos/'.$photo->album_id.'/'.$photo->photo)){
            $photo->delete();
            return redirect('/albums/'.$photo->album_id)->with('success', 'Photo Deleted');
        }
    }
}

