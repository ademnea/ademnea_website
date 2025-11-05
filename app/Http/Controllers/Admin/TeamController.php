<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests;

use App\Models\Team;
use App\Models\Task;
use Illuminate\Http\Request;


class TeamController extends Controller
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
            $team = Team::where('name', 'LIKE', "%$keyword%")
                ->orWhere('description', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $team = Team::latest()->paginate($perPage);
        }

        return view('admin.team.index', compact('team'));
    }

    public function team()
    {
        // Fetch researchers and interns from the same Team model you already use
        $researchers = Team::where('title', 'Researcher')->get();
        $phdStudents = Team::where('title', 'PhD Student')->get();
        $interns = Team::where('title', 'Intern')->get();

        // Return the blade view you already have (resources/views/website/team.blade.php)
        return view('website.team', compact('researchers', 'phdStudents', 'interns'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {

        return view('admin.team.create');
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
            'name'=>'required|max:255',
            'title'=>'required|max:255',
            'description'=>'required',
            'image'=>'required|mimes:jpg,png,jpeg|max:5048'
        ]);

        $newImageName = time() . '-' . $request->name . '.' . $request->image->extension();
        $request->image->move(public_path('images'), $newImageName);
        $requestData = $request->all();
        $requestData['image_path'] = $newImageName;

        
        Team::create($requestData);

        return redirect('admin/team')->with('flash_message', 'Team added!');
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
       
        $team = Team::findOrFail($id);

        return view('admin.team.show', compact('team'));
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
        $team = Team::findOrFail($id);

        return view('admin.team.edit', compact('team'));
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
        
        $team = Team::findOrFail($id);
        $team->update($requestData);

        return redirect('admin/team')->with('flash_message', 'Team updated!');
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
        Team::destroy($id);

        return redirect('admin/team')->with('flash_message', 'Team deleted!');
    }
}
