<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GalleryIntern;
use App\Models\GalleryPhoto;

class GalleryInternController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Fetch all gallery interns with their photos
        $galleries = GalleryIntern::with('photos')->orderBy('date', 'desc')->get();

        return view('admin.gallery_intern.index', compact('galleries'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
public function store(Request $request)
{
    // Validate inputs
    $request->validate([
        'title' => 'required|string|max:255',
        'venue' => 'required|string|max:255',
        'date' => 'required|date',
        'description' => 'nullable|string',
        'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:20048', // validate each image
    ]);

    // Create the gallery record
    $gallery = GalleryIntern::create([
        'title' => $request->title,
        'venue' => $request->venue,
        'date' => $request->date,
        'description' => $request->description,
    ]);

    // Store each uploaded image and create a GalleryPhoto record
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $filename = time() . '_' . $image->getClientOriginalName();
            $path = $image->storeAs('gallery', $filename, 'public');

            GalleryPhoto::create([
                'gallery_intern_id' => $gallery->id,
                'photo_path' => $path,
            ]);
        }
    }

    // Redirect back or return response
    return redirect()->back()->with('success', 'Gallery and images uploaded successfully.');
}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
  public function edit(GalleryIntern $gallery)
{
    return view('admin.gallery_intern.edit', compact('gallery'));
}

public function update(Request $request, GalleryIntern $gallery)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'venue' => 'nullable|string',
        'date' => 'nullable|date',
        'description' => 'nullable|string',
        'images.*' => 'nullable|image|max:2048',
    ]);

    $gallery->update($validated);


    // Redirect back with success message
    return redirect()->back()->with('success', 'Gallery updated successfully!');
}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
 public function destroy($id)
{
    $gallery = GalleryIntern::findOrFail($id);
    $gallery->delete();
    
    return redirect()->back()->with('success', 'Gallery deleted successfully');
}
}
