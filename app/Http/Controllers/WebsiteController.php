<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\ResearchProfile;
use App\Models\Gallery;
use App\Models\WorkPackage;
use App\Models\Event;


class WebsiteController extends Controller
{
    public function index(){
        $gallery = Gallery::all();
        $researchers = Team::where('title', 'Researcher')->get();
        $interns = Team::where('title', 'Intern')->get();
        $events = Event::latest()->get();


        

        return view('website.layouts', [
            'researchers' => $researchers,
            'interns' => $interns,
            'gallery' => $gallery,
            'events' => $events, // ✅ Now it's passed to the view!
        ]);
    }

    public function sudan(){
        $profile = ResearchProfile::where('category', 'masters')->where('country', 'sudan')->get();

        return view('website.mastersprofile',
        [
            'profile'=>$profile           
        ]
    );
    }
    public function uganda(){
        $profile = ResearchProfile::where('category', 'masters')->where('country', 'uganda')->get();

        return view('website.mastersprofile', compact("profile"));
    }
    public function tanzania(){
        $profile = ResearchProfile::where('category', 'masters')->where('country', 'tanzania')->get();

        return view('website.mastersprofile',
        [
            'profile'=>$profile           
        ]
    );
    }

   public function wp1()
        {
            $workpackages = WorkPackage::where('name', 'like', '%Networks and Resilience%')->get();
            return view('website.workpackages', ['workpackages' => $workpackages]);
        }

        public function wp2()
        {
            $workpackages = WorkPackage::where('name', 'like', '%Sensors and signal processing%')->get();
            return view('website.workpackages', ['workpackages' => $workpackages]);
        }

        public function wp3()
        {
            $workpackages = WorkPackage::where('name', 'like', '%Data Analytics for Environment Monitoring%')->get();
            return view('website.workpackages', ['workpackages' => $workpackages]);
        }

        public function wp4()
        {
            $workpackages = WorkPackage::where('name', 'like', 'Project Management%')->get();
            return view('website.workpackages', ['workpackages' => $workpackages]);
        }
   }
