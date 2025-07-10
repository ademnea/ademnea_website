<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use App\Models\Farmer;
use App\Models\User; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class FarmerController extends Controller
{
    public function index(Request $request)
    {        
        $perPage = 25;
        $farmer = Farmer::with('user')->latest()->paginate($perPage);
        return view('admin.farmer.index', compact('farmer'));
    }

    public function create()
    {
        return view('admin.farmer.create');
    }

    public function store(Request $request)
    {
        $requestData = $request->all();

        // Create a new user
        $user = User::create([
            'name' => $requestData['fname'] . ' ' . $requestData['lname'],
            'email' => $requestData['email'],
            'password' => Hash::make($requestData['password']),
            'role' => 'farmer',
        ]);

        // Create a new farmer associated with the user
        $farmer = Farmer::create([
            'user_id' => $user->id,
            'fname' => $requestData['fname'],
            'lname' => $requestData['lname'],
            'gender' => $requestData['gender'],
            'address' => $requestData['address'],
            'telephone' => $requestData['telephone'],
        ]);

        return redirect('admin/farmer')->with('flash_message', 'Farmer added!');
    }

    public function show($id)
    {
        $farmer = Farmer::with('user')->findOrFail($id);
        return view('admin.farmer.show', compact('farmer'));
    }

    public function edit($id)
    {
        $farmer = Farmer::with('user')->findOrFail($id);
        return view('admin.farmer.edit', compact('farmer'));
    }

    public function update(Request $request, $id)
    {
        $requestData = $request->all();
        
        $farmer = Farmer::with('user')->findOrFail($id);
        $farmer->update($requestData);
    
        // Update the associated user
        $farmer->user->update([
            'name' => $requestData['fname'] . ' ' . $requestData['lname'],
            'email' => $requestData['email'],
            // Only update the password if a new one is set
            'password' => $requestData['password'] ? Hash::make($requestData['password']) : $farmer->user->password,
        ]);
    
        return redirect('admin/farmer')->with('flash_message', 'Farmer updated!');
    }

    public function destroy($id)
    {
        Farmer::destroy($id);
        return redirect('admin/farmer')->with('flash_message', 'Farmer deleted!');
    }
}