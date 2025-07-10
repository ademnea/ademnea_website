<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use App\Models\Publication;
use App\Models\WorkPackage;


class DisplayPublicationController extends Controller
{

  // Controller responsible for displaying publications
  public function displayPublication()
  {
      $workpackages = WorkPackage::get();   
      $publication= Publication::all();
      return view('website.publication', ['publication'=> $publication,'workpackages'=>$workpackages,]);

  }
  
}