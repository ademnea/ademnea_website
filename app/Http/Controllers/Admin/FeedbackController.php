<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index()
    {
        $feedback = Feedback::latest()->paginate(25);
        return view('admin.feedback.index', compact('feedback'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|max:255',
            'message' => 'required'
        ]);

        Feedback::create($request->all());

        return back()->with('success', 'Your message has been sent successfully!');
    }

    public function destroy($id)
    {
        Feedback::destroy($id);
        return redirect('admin/feedback')->with('flash_message', 'Feedback deleted!');
    }
}