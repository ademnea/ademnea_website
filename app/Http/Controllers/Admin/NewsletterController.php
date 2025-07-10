<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use Validator;
use Response;
use File;
use App\Models\Newsletter;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $perPage = 10;
        $search = $request->input('search');
        
        $query = Newsletter::query();
        
        if ($search) {
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }
        
        $newsletter = $query->paginate($perPage);
        
        return view('admin.newsletter.index', compact('newsletter'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.newsletter.create');

        
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
        
        Newsletter::create($requestData);

        return redirect('admin/newsletter')->with('flash_message', 'Article added!');
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
        $newsletter = Newsletter::findOrFail($id);

        return view('admin.newsletter.show', compact('newsletter'));
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
        $newsletter = Newsletter::findOrFail($id);

        return view('admin.newsletter.edit', compact('newsletter'));
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
        
        $newsletter = Newsletter::findOrFail($id);
        $newsletter->update($requestData);

        return redirect('admin/newsletter')->with('flash_message', 'Newsletter updated!');
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
        Newsletter::destroy($id);

        return redirect('admin/newsletter')->with('flash_message', 'Newsletter deleted!');
    }
    public function upload(Request $request)
    {
        if($request->hasFile('upload')) {
            $originName = $request->file('upload')->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $extension = $request->file('upload')->getClientOriginalExtension();
            $fileName = $fileName.'_'.time().'.'.$extension;
        
            $request->file('upload')->move(public_path('images'), $fileName);
            $CKEditorFuncNum = $request->input('CKEditorFuncNum');
            $url = asset('images/'.$fileName); 
            $msg = 'Image uploaded successfully'; 
            $response = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";
            //append to the json file
                    $file = file_get_contents('images_list.json', true);
             $data = json_decode($file,true);
             unset($file);

             $path="/images/".$fileName;
             //you need to add new data as next index of data.
             $data[] = array('image' => $path);
             $result=json_encode($data);
             file_put_contents('images_list.json', $result);
             unset($result);

            @header('Content-type: text/html; charset=utf-8'); 
            echo $response;
        }
}
}