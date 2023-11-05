<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function list(Request $request) {
        $admin = session()->get('admin');
        $id = str_split($admin);

        $katakunci = $request->katakunci;
        if(strlen($katakunci)) {
            $data = Admin::where('name','like','%'.$katakunci.'%')->whereNotIn('id',$id)->paginate(10);
        } else {
            $data = Admin::whereNotIn('id',$id)->paginate(10);
        }
        return view('admin.list-admin')->with('data',$data);
    }

    public function create() {
        return view('admin.create-admin');
    }

    public function create_process(Request $request) {
        $request->validate([
            "name" => "required",
            "photo" => "max:3048",
            "phone_or_email" => "required",
            "password" => "required | min:6",
            "confirm_password" => "required | min:6 | same:password"
        ],[
            "name.required" => "Name is required!",
            "photo.max" => "Photo size must be less than 3MB!",
            "phone_or_email.required" => "Phone Number or Email is required!",
            "password.required" => "Password is required!",
            "password.min" => "Password must contain 6 characters or more",
            "confirm_password.required" => "Confirm Password is required!",
            "confirm_password.min" => "Confirm Password must contain 6 characters or more",
            "confirm_password.same" => "Confirm Password doesn't match password"
        ]);

        if (preg_match('/[a-zA-Z]/', $request->phone_or_email)) {
            $request->validate([
                "phone_or_email" => "email",
            ],[
                "phone_or_email.email" => "Email must be a valid email address!"
            ]);

            $same = Admin::where('phone_or_email',$request->phone_or_email)->first();
            if($same) {
                return redirect('admin/create')->with('credentials','Email is already in use!');
            }
        } else {
            $request->validate([
                "phone_or_email" => "numeric | digits_between:7,15",
            ],[
                "phone_or_email.numeric" => "Phone number must be number!",
                "phone_or_email.digits_between" => "Phone number must be between 7 and 15 digits!"
            ]);

            $code = substr($request->phone_or_email, 0, 1);
            if($code === "0") {
                return redirect('admin/create')->with('credentials','Country code on the phone number is invalid!');
            }

            $same = Admin::where('phone_or_email',$request->phone_or_email)->first();
            if($same) {
                return redirect('admin/create')->with('credentials','Phone Number is already in use!');
            }
        }

        $file = $request->file('photo');
        if($file) {
            $format = $file->getClientOriginalExtension();
            if(strtolower($format) === 'jpg' || strtolower($format) === 'jpeg' || strtolower($format) === 'png') {
                $photo = $request->file('photo')->store('photo');
            } else {
                return redirect('admin/create')->with('format','The photo format must be jpg, jpeg, or png!');
            }
        } else {
            $photo = null;
        }

        Admin::create([
            "name" => $request->name,
            "photo" => $photo,
            "phone_or_email" => $request->phone_or_email,
            "password" => bcrypt($request->password)
        ]);

        return redirect('admin/list')->with('success','Admin successfully created');
    }

    public function edit($id) {
        $data = Admin::find($id);

        return view('admin.edit-admin')->with('data',$data);
    }

    public function edit_process(Request $request, $id) {
        $data = Admin::find($id);

        $request->validate([
            "name" => "required",
            "photo" => "max:3048",
            "phone_or_email" => "required",
        ],[
            "name.required" => "Name is required!",
            "photo.max" => "Photo size must be less than 3MB!",
            "phone_or_email.required" => "Phone Number or Email is required!",
        ]);

        if (preg_match('/[a-zA-Z]/', $request->phone_or_email)) {
            $request->validate([
                "phone_or_email" => "email",
            ],[
                "phone_or_email.email" => "Email must be a valid email address!"
            ]);

            $same = Admin::where('phone_or_email',$request->phone_or_email)->whereNotIn('id',[$id])->first();
            if($same) {
                return redirect('admin/edit/'.$id)->with('credentials','Email is already in use!');
            }
        } else {
            $request->validate([
                "phone_or_email" => "numeric | digits_between:7,15",
            ],[
                "phone_or_email.numeric" => "Phone number must be number!",
                "phone_or_email.digits_between" => "Phone number must be between 7 and 15 digits!"
            ]);

            $code = substr($request->phone_or_email, 0, 1);
            if($code === "0") {
                return redirect('admin/edit/'.$id)->with('credentials','Country code on the phone number is invalid!');
            }

            $same = Admin::where('phone_or_email',$request->phone_or_email)->whereNotIn('id',[$id])->first();
            if($same) {
                return redirect('admin/edit/'.$id)->with('credentials','Phone Number is already in use!');
            }
        }

        $file = $request->file('photo');
        if($file) {
            $format = $file->getClientOriginalExtension();
            if(strtolower($format) === 'jpg' || strtolower($format) === 'jpeg' || strtolower($format) === 'png') {
                $photo = $request->file('photo')->store('photo');
            } else {
                return redirect('admin/edit/'.$id)->with('format','The photo format must be jpg, jpeg, or png!');
            }
        } else {
            $photo = $data->photo;
        }

        if($request->password !== null) {
            $request->validate([
                "password" => "min:6",
                "confirm_password" => "min:6 | same:password"
            ],[
                "password.min" => "Password must contain 6 characters or more",
                "confirm_password.min" => "Confirm Password must contain 6 characters or more",
                "confirm_password.same" => "Confirm Password doesn't match password"
            ]);

            Admin::where('id',$id)->update([
                "name" => $request->name,
                "photo" => $photo,
                "phone_or_email" => $request->phone_or_email,
                "password" => bcrypt($request->password)
            ]);

        } else if($request->password === null) {
            Admin::where('id',$id)->update([
                "name" => $request->name,
                "photo" => $photo,
                "phone_or_email" => $request->phone_or_email,
            ]);
        }

        return redirect('admin/list')->with('success','Admin successfully edited');
    }

    public function delete($id) {
        Admin::where('id',$id)->delete();

        return redirect('admin/list')->with('success','Admin successfully deleted');
    }
}