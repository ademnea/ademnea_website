<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use App\Models\Newsletter;
use App\Models\WorkPackage;

class DisplayNewsletterController extends Controller
{
    // Controller responsible for displaying newsletters
    public function displayNewsletter()
    {
        $workpackages = WorkPackage::get();   
        $newsletter= Newsletter::all();
        return view('website.newsletter', ['newsletter'=> $newsletter,'workpackages'=>$workpackages,]);

    }
    
}
