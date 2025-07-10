<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests;

use App\Models\Task;
use App\Models\WorkPackage;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create($id)
    {
        $workpackage = WorkPackage::findOrFail($id);
        return view('admin.tasks.create', compact('workpackage'));
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

        
        Task::create($requestData);

        return redirect('admin/work-package')->with('flash_message', 'Task added!');
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
        $task = Task::findOrFail($id);

        return view('admin.tasks.show', compact('task'));
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
        $task = Task::findOrFail($id);

        return view('admin.tasks.edit', compact('task'));
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
        
        $task = Task::findOrFail($id);
        $task->update($requestData);

        return redirect('admin/task')->with('flash_message', 'Task updated!');
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
        Task::destroy($id);

        return redirect('admin/task')->with('flash_message', 'Task deleted!');
    }
}
