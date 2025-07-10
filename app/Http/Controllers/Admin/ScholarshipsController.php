<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use Illuminate\Http\Request;

class ScholarshipsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 25;

        if (!empty($keyword)) {
            $scholarship = Scholarship::where('title', 'LIKE', "%$keyword%")
                ->orWhere('description', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $scholarship = Scholarship::latest()->paginate($perPage);
        }

        return view('admin.scholarship.index', compact('scholarship'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.scholarship.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|max:255',
            'description' => 'required',
            'instructions' => 'required'
        ]);

        $data = $request->only(['title', 'description', 'instructions']);
        Scholarship::create($data);

        return redirect('admin/scholarship')->with('flash_message', 'Scholarship added!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $scholarship = Scholarship::findOrFail($id);
        return view('admin.scholarship.show', compact('scholarship'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $scholarship = Scholarship::findOrFail($id);
        return view('admin.scholarship.edit', compact('scholarship'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required|max:255',
            'description' => 'required',
            'instructions' => 'required'
        ]);

        $data = $request->only(['title', 'description', 'instructions']);
        $scholarship = Scholarship::findOrFail($id);
        $scholarship->update($data);

        return redirect('admin/scholarship')->with('flash_message', 'Scholarship updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Scholarship::destroy($id);
        return redirect('admin/scholarship')->with('flash_message', 'Scholarship deleted!');
    }
}
