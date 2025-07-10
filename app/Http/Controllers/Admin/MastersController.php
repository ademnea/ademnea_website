<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkPackage;
use App\Models\Team;
use App\Models\Gallery;
use App\Models\Scholarship;

class MastersController extends Controller
{
    public function sudan(){
        $workpackages = WorkPackage::get();
        $scholarship = Scholarship::get()->where('category', 'masters')->where('country', 'sudan');

        return view('website.masterslayout',
        [
            'workpackages'=>$workpackages,
            'scholarships'=>$scholarship           
        ]
    );
    }
    public function uganda(){
        $workpackages = WorkPackage::get();
        $scholarship = Scholarship::get()->where('category', 'masters')->where('country', 'uganda');

        return view('website.masterslayout',
        [
            'workpackages'=>$workpackages,
            'scholarships'=>$scholarship           
        ]
    );
    }
    public function tanzania(){
        $workpackages = WorkPackage::get();
        $scholarship = Scholarship::get()->where('category', 'masters')->where('country', 'tanzania');

        return view('website.masterslayout',
        [
            'workpackages'=>$workpackages,
            'scholarships'=>$scholarship           
        ]
    );
    }
}
