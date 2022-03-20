<?php

namespace Tests\Feature;

use App\Http\Controllers\AdminController;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_add_product()
    {
        $user = User::create([
            "name" => "rand_name",
            "lastname" => "rand_lastname",
            "email" => "rand@gmail.com",
            "password" => Hash::make("something random"),
        ]);
        $user->role = "ADMIN";
        $request = $this->actingAs($user, "web")->post("/api/admin/new_product", [
            "product_name" => "fake_name",
            "available" => 1,
            "available_number" => "210"
        ]);
        $request->assertStatus(201);
        $this->assertDatabaseCount(Product::find(1), 1);
    }

    public function test_edit_product()
    {
        $user = User::create([
            "name" => "rand_name",
            "lastname" => "rand_lastname",
            "email" => "rand@gmail.com",
            "password" => Hash::make("something random")
        ]);
        $product = Product::create([
            "name" => "fake_name",
            "available" => 1,
            "available_number" => "210"
        ]);
        $product->save();
        $user->role = "ADMIN";
        $request = $this->actingAs($user, "web")->put("/api/admin/edit_product/$product->id", [
            "new_name" => "new_name",
            "available" => 1,
            "available_number" => 20
        ]);
        $request->assertOk();
        $found_product = Product::find($product->id);
        $this->assertTrue($found_product->name == "new_name" && $found_product->available == 1
            && $found_product->available_number == 20);
    }

    public function test_delete_product()
    {
        $user = User::create([
            "name" => "fake_name",
            "lastname" => "fake_lastname",
            "email" => "fake_email",
            "password" => "fake_password"
        ]);
        $user->role = "ADMIN";
        $user->save();
        $product = Product::create([
            "name" => "fake_name",
            "available" => 1,
            "available_number" => "210"
        ]);
        $product->save();
        $request = $this->actingAs($user, "web")->delete("api/admin/delete_product/$product->id");
        $request->assertOk();
        $this->assertDatabaseCount($product, 0);
    }
}
