<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkPackage;

class WorkpackagesController extends Controller
{
    //
    public function workpackages($id)
    {
        $workpackages = WorkPackage::get()->where('id', $id);
        return view('website.workpackages', [
            'workpackages'=>$workpackages      
        ]);
    }
}
