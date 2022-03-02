<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
        if ($user != null)
            return response()->json(["message" => "user successfully created"], 200);
        else
            return response()->json(["message" => "something went wrong"], 400);
    }
}
