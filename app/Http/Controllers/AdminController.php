<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{

    public function addProduct(): \Illuminate\Http\JsonResponse
    {
        $checked = \request()->validate([
            "product_name" => "required",
            "available" => "required",
            "available_number" => "required"
        ]);
        $product = Product::create([
            "name" => $checked["product_name"],
            "available" => $checked["available"],
            "available_number" => $checked["available_number"]
        ]);
        $product->save();
        return response()->json([
            "message" => "product successfully added"
        ], 201);
    }

    public function edit_product($id): \Illuminate\Http\JsonResponse
    {
        $check = \request()->validate([
            "new_name" => "required|min:3",
            "available" => "required",
            "available_number" => "required|int"
        ]);
        $product = Product::find($id);
        if ($product == null)
            return response()->json(["message" => "product not found"], 404);
        $product->name = $check["new_name"];
        $product->available = $check["available"];
        $product->available_number = $check["available_number"];
        $product->save();
        return response()->json(["product edited"], 200);
    }

    public function delete_product($id): \Illuminate\Http\JsonResponse
    {
        $product = Product::find($id);
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
}
