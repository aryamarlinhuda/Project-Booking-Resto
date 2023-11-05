<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login_form() {
        if(session()->has('admin')) {
            return redirect('destination/list')->with('success','Welcome Back!');
        } else {
            return view('login');
        }
    }

    public function login(Request $request) {
        $request->validate([
            "phone_or_email" => "required",
            "password" => "required | min:6"
        ],[
            "phone_or_email.required" => "Phone Number or Email is required!",
            "password.required" => "Password is required!",
            "password.min" => "Password must contain 6 characters or more!" 
        ]);

        if (preg_match('/[a-zA-Z]/', $request->phone_or_email)) {
            $request->validate([
                "phone_or_email" => "email",
            ],[
                "phone_or_email.email" => "Email must be a valid email address!"
            ]);
        } else {
            $request->validate([
                "phone_or_email" => "numeric | digits_between:7,15",
            ],[
                "phone_or_email.digits_between" => "Phone number must be between 7 and 15 digits!"
            ]);
        }

        $admin = Admin::where('phone_or_email',$request->phone_or_email)->first();
        if(!$admin) {
            return redirect('/')->with('not_found','Phone Number or Email not found!');
        }

        if(!Hash::check($request->password,$admin->password)) {
            return redirect('/')->with('error','Wrong password!');
        } else {
            $id = $admin->id;
            session()->put('admin',$id);
            session()->put('name',$admin->name);
            session()->put('photo',$admin->photo);
            return redirect('destination/list')->with('success','Login Successfully');
        }
    }

    public function logout() {
        session()->flush();

        return redirect('/')->with('logout','Logout Successfully');
    }
}
