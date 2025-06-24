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
                ->orWhere('title', 'LIKE', "%$keyword%")
                ->orWhere('category', 'LIKE', "%$keyword%")
                ->orderBy('category')
                ->orderBy('name')
                ->get();
        } else {
            $team = Team::orderBy('category')
                ->orderBy('name')
                ->get();
        }

        return view('admin.team.index', compact('team'));
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
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|in:Leadership,Senior Researchers,Researchers,Interns,Alumni',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:5048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . str_replace(' ', '_', $request->name) . '.' . $image->getClientOriginalExtension();
            
            // Ensure the directory exists
            $destinationPath = public_path('images/team');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            
            $image->move($destinationPath, $imageName);
            $validatedData['image_path'] = 'team/' . $imageName;
        }

        Team::create($validatedData);

        return redirect('admin/team')
            ->with('success', 'Team member added successfully!');
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
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|in:Leadership,Senior Researchers,Researchers,Interns,Alumni',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:5048',
        ]);

        $team = Team::findOrFail($id);
        
        // Handle image upload if a new image is provided
        if ($request->hasFile('image')) {
            // Delete old image if it exists
            $oldImagePath = public_path('images/' . $team->image_path);
            if ($team->image_path && file_exists($oldImagePath)) {
                try {
                    unlink($oldImagePath);
                } catch (\Exception $e) {
                    // Log error but don't stop execution
                    \Log::error('Failed to delete old image: ' . $e->getMessage());
                }
            }
            
            // Upload new image
            $image = $request->file('image');
            $imageName = time() . '_' . str_replace(' ', '_', $request->name) . '.' . $image->getClientOriginalExtension();
            
            // Ensure the directory exists
            $destinationPath = public_path('images/team');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            
            $image->move($destinationPath, $imageName);
            $validatedData['image_path'] = 'team/' . $imageName;
        } else {
            // Remove image from validated data if not being updated
            unset($validatedData['image']);
        }
        
        // Update the team member with validated data
        $team->update($validatedData);

        return redirect('admin/team')
            ->with('success', 'Team member updated successfully!');
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
        $team = Team::findOrFail($id);
        
        // Delete the associated image if it exists
        if ($team->image_path) {
            $imagePath = public_path('images/' . $team->image_path);
            if (file_exists($imagePath)) {
                try {
                    unlink($imagePath);
                } catch (\Exception $e) {
                    // Log error but don't stop execution
                    \Log::error('Failed to delete team member image: ' . $e->getMessage());
                }
            }
        }
        
        // Delete the team member
        $team->delete();

        return redirect('admin/team')
            ->with('success', 'Team member deleted successfully!');
    }
}
