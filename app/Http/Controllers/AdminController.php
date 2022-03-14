<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

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
}
