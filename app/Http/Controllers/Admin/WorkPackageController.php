<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests;

use App\Models\WorkPackage;
use Illuminate\Http\Request;

class WorkPackageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 25;

        if (!empty($keyword)) {
            $workpackage = WorkPackage::where('name', 'LIKE', "%$keyword%")
                ->orWhere('abbreviation', 'LIKE', "%$keyword%")
                ->orWhere('description', 'LIKE', "%$keyword%")
                
                ->latest()->paginate($perPage);
        } else {
            $workpackage = WorkPackage::oldest()->paginate($perPage);
        }

        return view('admin.work-package.index', compact('workpackage'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.work-package.create');
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
        
        WorkPackage::create($requestData);

        return redirect('admin/work-package')->with('flash_message', 'WorkPackage added!');
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
        $workpackage = WorkPackage::findOrFail($id);

        return view('admin.work-package.show', compact('workpackage'));
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
        $workpackage = WorkPackage::findOrFail($id);

        return view('admin.work-package.edit', compact('workpackage'));
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
        
        $workpackage = WorkPackage::findOrFail($id);
        $workpackage->update($requestData);

        return redirect('admin/work-package')->with('flash_message', 'WorkPackage updated!');
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
        WorkPackage::destroy($id);

        return redirect('admin/work-package')->with('flash_message', 'WorkPackage deleted!');
    }
}
