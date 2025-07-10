<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;


class SensorMonitoringController extends Controller
{
    // Controller responsible for displaying newsletters
    public function sensorMonitor()
    {
        return view('admin.sensor-monitoring.index');
    }

    
    
}
