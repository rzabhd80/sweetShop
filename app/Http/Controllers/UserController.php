<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PharIo\Manifest\Email;

class UserController extends Controller
{
    /**
     * registration function function
     *
     * @return response
     */
    public function register()
    {
        $checked = request()->validate([
            "name" => "required",
            "lastname" => "required",
            "email" => "required|unique:users,email",
            "password" => "required|min:8"
        ]);

        $user = new User();
        $user->name = $checked["name"];
        $user->lastname = $checked["lastname"];
        $user->email = $checked["email"];
        $user->password = Hash::make($checked["password"]);
        $user->save();
        if ($user != null) {
            Mail::to(request()->user())->send(new WelcomeMail($user));
            return response()->json(["message" => "user successfully created"], 200);
        } else
            return response()->json(["message" => "something went wrong"], 400);
    }


    public function login()
    {
        $checked = request()->validate([
            "email" => "required|email",
            "password" => "required"
        ]);
        $user = User::where("email", $checked["email"])->first();
        if ($user == null)
            return response()->json([
                "message" => "user not found"
            ], 404);
        if (Hash::check($checked["password"], $user->password)) {
            $token = $user->createToken("auth_token")->plainTextToken;
            return response()->json(["message" => "successfully logged in", "token" => $token], 200);
            Auth::login($user);
        } else return response()->json(["message" => "incorrect password"], 403);
    }



    public function logout()
    {
        $user = User::where("email", request()->user()->email)->first();
        $user->tokens()->delete();
        $user->save();
        return response()->json(["message" => "successfully logged out"], 200);
    }
}
