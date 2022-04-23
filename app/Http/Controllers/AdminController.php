<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{

    public function addProduct(): \Illuminate\Http\JsonResponse
    {
        $checked = \request()->validate([
            "product_name" => "required",
            "available" => "required",
            "available_number" => "required",
            "price" => "required|integer"
        ]);
        $product = Product::create([
            "name" => $checked["product_name"],
            "available" => $checked["available"],
            "available_number" => $checked["available_number"],
            "price" => $checked["price"]
        ]);
        $product->save();
        return response()->json([
            "message" => "product successfully added"
        ], 201);
    }

    public function edit_product(): \Illuminate\Http\JsonResponse
    {
        $check = \request()->validate([
            "product_id" => "required|exists:products,id",
            "new_name" => "required|min:3",
            "available" => "required",
            "available_number" => "required|int",
            "price" => "required|integer"
        ]);
        $product = Product::find($check["product_id"]);
        if ($product == null)
            return response()->json(["message" => "product not found"], 404);
        $product->name = $check["new_name"];
        $product->available = $check["available"];
        $product->available_number = $check["available_number"];
        $product->price = $check["price"];
        $product->save();
        return response()->json(["product edited"], 200);
    }

    public function delete_product(): \Illuminate\Http\JsonResponse
    {
        $check_id = request()->validate([
            "product_id" => "required"
        ]);
        $product = Product::find($check_id["product_id"]);
        if ($product == null)
            return response()->json(["product not found"], 404);
        else {
            $product->delete();
            return response()->json(['product deleted'], 200);
        }
    }

    public function addUser(): \Illuminate\Http\JsonResponse
    {
        $check = \request()->validate([
            "name" => "required",
            "lastname" => "required",
            "email" => "required|email|unique:users,email",
            "password" => "required|min:8",
        ]);
        $user = new User([
            "name" => $check["name"],
            "lastname" => $check["lastname"],
            "email" => $check["email"],
            "password" => Hash::make($check["password"]),
        ]);
        $user->role = "USER";
        $user->save();
        return response()->json(["message" => "user successfully created"], 200);
    }
    public function addProdImg()
    {
        $check = request()->validate([
            "product_id" => "required|exists:products,id",
            "file" => "required|file|mimes:png,jpg,jpeg",
        ]);
        if ($check) {
            dd(request()->file());
            $file = request()->file()->store("public/images");
            $image = new Image();
            $image->imageable_id = Auth()->user()->id;
            $image->image_link = request()->file()->basename();
            $image->save();
        }
    }
}
