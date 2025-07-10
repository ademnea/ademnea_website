<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use Illuminate\Support\Facades\DB;
use App\Models\Newsletter;
use App\Models\WorkPackage;


class DisplayIndividualNewsletterController extends Controller
{
   // Controller responsible for displaying individual newsletters
   public function displayIndividualNewsletter()
   {
    //    $workpackages = WorkPackage::get();   
    //    $newsletter= Newsletter::all();
    //    return view('website.newsletter', ['newsletter'=> $newsletter,'workpackages'=>$workpackages,]);

         // $newsletter = Newsletter::findOrFail($id);

         // return view('website.individual_newsletter', compact('newsletter'));
    
   $workpackages = WorkPackage::get();   
   $newsletter= Newsletter::all();
   return view('website.individual_newsletter', ['newsletter'=> $newsletter,'workpackages'=>$workpackages,]);

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

        return view('website.individual_newsletter', ['newsletter'=> $newsletter]);
    }
}
