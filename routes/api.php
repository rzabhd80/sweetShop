<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\VerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get("/email/verify/{id}", [VerificationController::class, "verify"])->name("verification.verify");
Route::get("email/resend", [VerificationController::class, "resend"])->name("verification.resend");

Route::prefix("/auth")->group(function () {
    Route::post("/register", [UserController::class, "register"]);
    Route::post("/login", [UserController::class, "login"]);
    Route::post("/logout", [UserController::class, "logout"])->middleware("auth:sanctum");
});


Route::prefix("/users")->group(function () {
    Route::put("/edit_password", [UserController::class, "edit_pass"]);
    Route::put("/edit_email", [UserController::class, "edit_email"]);
});

Route::prefix("/admin")->middleware("adminRole")->group(function () {
    Route::post("/new_product", [\App\Http\Controllers\AdminController::class, "addProduct"]);
    Route::put("/edit_product/{id}", [\App\Http\Controllers\AdminController::class, "edit_product"]);
});
