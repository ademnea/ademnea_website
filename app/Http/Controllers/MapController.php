<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MapController extends Controller
{
    public function displayMap()
    {
        $hives = DB::table('hives')->get(); // get all hives
        $farms = DB::table('farms')->get(); // get all farms (if needed)

        return view('website.map', compact('hives', 'farms')); // update the view path as needed
    }
}
