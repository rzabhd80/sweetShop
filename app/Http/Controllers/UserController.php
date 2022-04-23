<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Js;
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
        if (!$checked)
            return response()->json(["message" => "credintials do not match"]);
        $user = new User();
        $user->name = $checked["name"];
        $user->lastname = $checked["lastname"];
        $user->email = $checked["email"];
        $user->password = Hash::make($checked["password"]);
        $user->save();
        if ($user != null) {
            // Mail::to(request()->user())->send(new WelcomeMail($user));
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
            auth()->login($user);
            $token = $user->createToken("auth_token")->plainTextToken;
            return response()->json(["message" => "successfully logged in", "token" => $token], 200);
        } else return response()->json(["message" => "incorrect password"], 403);
    }



    public function logout()
    {
        $user = User::where("email", request()->user()->email)->first();
        $user->tokens()->delete();
        $user->save();
        return response()->json(["message" => "successfully logged out"], 200);
    }

    public function edit_pass()
    {
        $checked =  request()->validate([
            "password_old" => "required",
            "new_password" => "required|min:8"
        ]);
        $user = User::where("email", auth()->user()->email)->first();
        if ($user == null)
            return response()->json(["message" => "user not found"], 403);
        $user->password = $checked["new_password"];
        $user->save();
        return response()->json([
            "message" => "password successfully updated",
        ], 200);
    }
    public function edit_email()
    {
        $checked = request()->validate([
            "new_email" => "required|unique:users,email"
        ]);
        $user = User::where("email", auth()->user()->email)->first();
        if ($user == null)
            return response()->json([
                "message" => "user not found"
            ], 404);
        $user->email = $checked["new_email"];
        $user->save();
        return response()->json([
            "message" => "email successfully updated",
            "new email" => $user->email
        ], 200);
    }
    public function buy_product()
    {
        $checked = request()->validate([
            "product_id" => "required|integer"
        ]);
        $product = Product::find($checked["product_id"]);
        if ($product) {
            $product->available_number -= 1;
            $product->save();
            return response()->json(["message" => "product bought"], 200);
        } else {
            return response()->json(["message" => "product not found"], 400);
        }
    }
}
