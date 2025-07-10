<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\WorkPackage;
use App\Models\ResearchProfile;


use Illuminate\Http\Request;

class ResearchProfileController extends Controller
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
            $profile = ResearchProfile::where('category', 'LIKE', "%$keyword%")
                ->orWhere('category', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $profile = ResearchProfile::latest()->paginate($perPage);
        }

        return view('admin.research-profile.index', compact('profile'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.research-profile.create');
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
        $this->validate($request, [
            'category'=>'required|max:2000',
            'description'=>'required'
        ]);

    
        $requestData = $request->all();

        
        ResearchProfile::create($requestData);

        return redirect('admin/research-profile')->with('flash_message', 'research-profile added!');
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
        $profile = ResearchProfile::findOrFail($id);

        return view('admin.research-profile.show', compact('profile'));
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
        $profile = ResearchProfile::findOrFail($id);

        return view('admin.research-profile.edit', compact('profile'));
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
        
        $profile = ResearchProfile::findOrFail($id);
        $profile->update($requestData);

        return redirect('admin/research-profile')->with('flash_message', 'research-profile updated!');
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
        ResearchProfile::destroy($id);

        return redirect('admin/research-profile')->with('flash_message', 'research-profile deleted!');
    }

//sudan phd layout
    public function sudan(){
        $workpackages = WorkPackage::get();
        $profile = ResearchProfile::get()->where('category', 'phd')->where('country', 'sudan');

        return view('website.phdprofile',
        [
            'workpackages'=>$workpackages,
            'profile'=>$profile           
        ]
    );
    }

    //uganda phd layout
    public function uganda(){
        $workpackages = WorkPackage::get();
        $profile = ResearchProfile::get()->where('category', 'phd')->where('country', 'uganda');

        return view('website.phdprofile',
        [
            'workpackages'=>$workpackages,
            'profile'=>$profile           
        ]
    );
    }
    //tanzania phd layout
    public function tanzania(){
        $workpackages = WorkPackage::get();
        $profile = ResearchProfile::get()->where('category', 'phd')->where('country', 'tanzania');

        return view('website.phdprofile',
        [
            'workpackages'=>$workpackages,
            'profile'=>$profile           
        ]
    );
    }
}
