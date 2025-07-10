<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;

class AuthController extends Controller
{
    public function dashboard()
    {
        return view('layouts.index');
    }
    public function loginForm()
    {
        return view('auth.login');
    }
    public function registerForm()
    {
        return view('auth.register');
    }
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:4',
            'email' => 'required|email|unique:users,email',
            'password' => 'min:6|required_with:password_confirmation|same:password_confirmation',
            'password_confirmation' => 'min:6'
        ]);
        // dd($this);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
        // $user->assignRole($request->role);

        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];
        if (Auth::guard('web')->attempt(['email' => $request->email, 'password' => $request->password])) {
        
            // if (Auth::user()->roles->pluck('name')[0]=='Guest') {
            //     return redirect('all-systems');
            // }

            return redirect('/admin/dashboard');
        } else {
            return redirect('/login');
        }
    }
 
    /**
     * Login
     */
    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }

    public function login(Request $request)
    {
        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];
 
        if (Auth::guard('web')->attempt(['email' => $request->email, 'password' => $request->password])) {
           
            // if (Auth::user()->roles->pluck('name')[0]!='Admin') {
            //     return redirect('all-systems');
            // }
            return redirect('/admin/dashboard');
        } else {
            return redirect('/login');
        }
    }
}
