<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TemperatureAnalyticsController extends Controller
{
    public function index(){
        return view('analytics.temperature_humidity.index');
    }
}
