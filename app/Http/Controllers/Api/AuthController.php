<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request) {
        try {
            $request->validate([
                "name" => "required",
                "phone_or_email" => "required",
                "password" => "required | min:6",
                "confirm_password" => "required | min:6 | same:password",
            ],[
                "name.required" => "Name is required!",
                "phone_or_email.required" => "Phone Number or Email is required",
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
            } else {
                $request->validate([
                    "phone_or_email" => "numeric | digits_between:7,15",
                ],[
                    "phone_or_email.digits_between" => "The phone number must be between 7 and 15 digits!",
                ]);
            }

            $same = User::where("phone_or_email",$request->input("phone_or_email"))->first();
            if($same) {
                return response()->json([
                    "status" => 400,
                    "message" => "Phone Number or Email already exists!",
                ], 400);
            }

            User::create([
                "name" => $request->input("name"),
                "phone_or_email" => $request->input("phone_or_email"),
                "password" => bcrypt($request->input("password")),
            ]);

            return response()->json([
                "status" => 200,
                "message" => "Registration successfully"
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                "status" => 400,
                "errors" => $e->errors(),
            ], 400);
        }
    }

    public function login(Request $request) {
        try {
            $credentials = $request->validate([
                "phone_or_email" => "required",
                "password" => "required | min:6",
            ],[
                "phone_or_email.required" => "Phone Number or Email is required!",
                "password.required" => "Password is required!",
                "password.min" => "Password must contain 6 characters or more",
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
                    "phone_or_email.digits_between" => "The Phone Number must be between 7 and 15 digits!",
                ]);
            }

            $user = User::where("phone_or_email",$request->input("phone_or_email"))->first();

            if(!$user) {
                return response()->json([
                    "status" => 400,
                    "message" => "Phone Number or Email not found!",
                ], 400);
            } else {
                if($user['photo']) {
                    $user->photo_profile = url("storage/".$user['photo']);
                } else {
                    $user->photo_profile = null;
                }
            }

            if(!Hash::check($request->password,$user->password)) {
                return response()->json([
                    "status" => 400,
                    "message" => "Wrong Password!"]
                    , 400
                );
            } else {
                $token = auth()->attempt($credentials);

                return response()->json([
                    "status" => 200,
                    "message" => "Login successfully",
                    "data" => $user,
                    "access_token" => $token
                ], 200);
            }

        } catch (ValidationException $e) {
            return response()->json([
                "status" => 400,
                "errors" => $e->errors(),
            ], 400);
        }
    }

    public function refresh_token() {
        $token = JWTAuth::getToken();
        $refresh_token = JWTAuth::refresh($token);
        return response()->json([
            'status' => 200,
            'message' => 'Refresh Token Successfully',
            'refresh_token' => $refresh_token,]
            ,200
        );
    }

    public function logout() {
        auth()->logout();
        
        return response()->json([
            "status" => 200,
            "message" => "Logout Successfully"
        ], 200);
    }
}
