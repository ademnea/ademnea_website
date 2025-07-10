<?php

namespace App\Http\Controllers;

use App\Models\GalleryIntern;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index()
    {
        // Eager load related photos for each gallery/event
        $galleries = GalleryIntern::with('photos')->get();

        return view('gallery', compact('galleries'));
    }
}

