<?php

namespace App\Http\Controllers\Admin;
use App\Models\Album;
//use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlbumsController extends Controller
{
    //
    public function index(){

        $album = Album::with('Photos')->get();

        return view('admin/albums.index')->with('albums', $album);
    }

    public function create(){
        return view('admin/albums.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'cover_image' => 'image|max:1999',
        ]);
        // Get filename with extension
        $filenameWithExt =  $request->file('cover_image')->getClientOriginalName();

        // Get just the filename
        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);

        //Get extension
        $extension = $request->file('cover_image')->getClientOriginalExtension();

        // Create new filename
        $filenameToStore = $filename.'_'.time().'.'.$extension;

        //Upload image
        $path = $request->file('cover_image')->storeAs('public/album_covers', $filenameToStore);

        // Create album
        $album = new Album;
        $album->name = $request->input('name');
        $album->description = $request->input('description');
        $album->cover_image = $filenameToStore;

        $album->save();

        return redirect('/albums')->with('success', 'Album Created');

    }
    public function show($id){
        $album = Album::with('Photos')->find($id);
        if(Auth::check())
        {
           // return Redirect::action('PagesController@index');
            return view('admin/albums.show')->with('album', $album);
        }
        else{
            return view('website/album_show')->with('album', $album);
        }
    }



}
