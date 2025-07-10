<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Farm;
use Illuminate\Support\Facades\DB;
use App\Models\Hive;
use Illuminate\Http\Request;


class HiveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Get the farm_id from the request query
        $farmId = $request->query('farm_id');
    
        // If farm_id is not provided or is invalid, you can return a message or redirect to a default page
        if (!$farmId || !Farm::find($farmId)) {
            return redirect()->route('farms.index')->with('error', 'Farm not found or invalid farm ID.');
        }
    
        // Store the farm_id in session
        session(['farm_id' => $farmId]);
    
        // Get hives associated with the farm
        $hive = Hive::where('farm_id', $farmId)->get();
    
        // Get all farms (optional if you need to show all farms in the view)
        $farms = Farm::all();
    
        // Return the view with the hives and farms data
        return view('admin.hives.index', compact('hive', 'farms'));
    }
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {

        return view('admin.hives.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $requestData = $request->all();

        $farm = $request->input("farm_id");
              
        Hive::create($requestData);

       // hive?farm_id=2

        return redirect('admin/hive?farm_id='.$farm)->with('flash_message', 'Hive added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
       
        $hive = Hive::findOrFail($id);

        return view('admin.hives.show', compact('hive'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $hive = Hive::findOrFail($id);

        return view('admin.hives.edit', compact('hive'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request)
    {
       // return $request->input();
       $latitude = $request->input('latitude');
       $longitude = $request->input('longitude');
       $hive_id = $request->input('hive_id');

      DB::table('hives')
        ->where('id',$hive_id)
        ->update([
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);

        return redirect('admin/hive')->with('flash_message', 'Farm updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        Hive::destroy($id);

        return redirect('admin/hive')->with('flash_message', 'Farm deleted!');
    }
}
