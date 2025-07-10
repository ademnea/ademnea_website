<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkPackage;
use App\Models\Team;
use App\Models\Gallery;
use App\Models\Scholarship;

class PhdController extends Controller
{
    public function sudan(){
        $workpackages = WorkPackage::get();
        $scholarship = Scholarship::get()->where('category', 'phd')->where('country', 'sudan');

        return view('website.phdlayout',
        [
            'workpackages'=>$workpackages,
            'scholarships'=>$scholarship           
        ]
    );
    }
    public function uganda(){
        $workpackages = WorkPackage::get();
        $scholarship = Scholarship::get()->where('category', 'phd')->where('country', 'uganda');

        return view('website.phdlayout',
        [
            'workpackages'=>$workpackages,
            'scholarships'=>$scholarship           
        ]
    );
    }
    public function tanzania(){
        $workpackages = WorkPackage::get();
        $scholarship = Scholarship::get()->where('category', 'phd')->where('country', 'tanzania');

        return view('website.phdlayout',
        [
            'workpackages'=>$workpackages,
            'scholarships'=>$scholarship           
        ]
    );
    }
}
