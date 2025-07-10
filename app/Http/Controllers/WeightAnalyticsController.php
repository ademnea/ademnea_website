<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WeightAnalyticsController extends Controller
{
    public function index(){
        return view('analytics.weight_analytics.index');
    }
}
