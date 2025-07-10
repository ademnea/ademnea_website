<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use App\Models\Farm;
use App\Models\Farmer;
use Illuminate\Http\Request;


class FarmController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {        
        $perPage = 25;
       
        $farmers = Farmer::all();

        //return $farmers;
        $farm = Farm::latest()->paginate($perPage);

        return view('admin.farm.index', compact('farm','farmers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $farmers = Farmer::all();

        return view('admin.farm.create',compact('farmers'));
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
      /*  $this->validate($request, [
            'ownerid'=>'required|max:255',
            'name'=>'required|max:255',
            'district'=>'required',
            'image'=>'required|mimes:jpg,png,jpeg|max:5048'
        ]);*/

        $requestData = $request->all();
              
        Farm::create($requestData);

        return redirect('admin/farm')->with('flash_message', 'Farm added!');
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
       
       
        $farm = Farm::findOrFail($id);

        return view('admin.farm.show', compact('farm'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit(Request $req, $id)
    {
       // return $req->input();

        $farm = Farm::findOrFail($id);

        return view('admin.farm.edit', compact('farm'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        
        $requestData = $request->all();
        
        $farm = Farm::findOrFail($id);
        $farm->update($requestData);

        return redirect('admin/farm')->with('flash_message', 'Farm updated!');
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
        Farm::destroy($id);

        return redirect('admin/farm')->with('flash_message', 'Farm deleted!');
    }


    
}
