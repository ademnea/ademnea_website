<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use App\Models\Newsletter;
use App\Models\WorkPackage;

class ArticleController extends Controller
{
    // Controller responsible for displaying newsletters
    public function index(Request $request, $id)
    {
        $workpackages = WorkPackage::get();   
        $newsletter= Newsletter::all();
        $article = Newsletter::where('id', $id)->get();
        return view('website.article', ['workpackages'=>$workpackages, 'article'=>$article, 'newsletter'=> $newsletter]);

    }
    
}
