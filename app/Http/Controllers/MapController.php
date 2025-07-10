<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Response;


class MapController extends Controller
{
    // Controller responsible for displaying newsletters
    // public function displayMap()
    // {
    //     return view('admin.map.index');
    // }

    public function displayMap($id)
    {
        $hives = DB::table('hives')->where('farm_id', $id)->get();
        $farm = DB::table('farms')->where('id', $id)->get();

        return view('admin.map.index', compact('farm', 'hives'));
    }
    
}
