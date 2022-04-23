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
            "available_number" => "210",
            "price" => "1000"
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
            "available_number" => "210",
            "price" => "1000"
        ]);
        $product->save();
        $user->role = "ADMIN";
        $request = $this->actingAs($user, "web")->put("/api/admin/edit_product/", [
            "product_id" => $product->id,
            "new_name" => "new_name",
            "available" => 1,
            "available_number" => 20,
            "price" => "1000"
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
            "available_number" => "210",
            "price" => "1000"
        ]);
        $product->save();
        $request = $this->actingAs($user, "web")->delete("api/admin/delete_product", ["product_id" => $product->id]);
        $request->assertOk();
        $this->assertDatabaseCount($product, 0);
    }

    public function test_add_user()
    {
        $admin = User::create([
            "name" => "fake_name",
            "lastname" => "fake_lastname",
            "email" => "fake_email@email.com",
            "password" => Hash::make("password"),
            "role" => "ADMIN"
        ]);
        $request = $this->actingAs($admin, "web")->post("/api/admin/add_user", [
            "name" => "fake_name",
            "lastname" => "fake_lastname",
            "email" => "sthRand@gmail.com",
            "password" => Hash::make("sthRandAsPassword"),
        ]);
        $request->assertOk();
        $user = User::where("email", "sthRand@gmail.com")->first();
        $this->assertNotNull($user);
    }
    public function test_admin_add_imageToPRoduct()
    {
        $admin = User::create([
            "name" => "randName",
            "lastname" => "randLastname",
            "email" => "rand_email",
            "password" => Hash::create("randPassword")
        ]);
        $product = Product::create([
            "name" => "fake_name",
            "available" => 1,
            "available_number" => "210",
            "price" => "1000"
        ]);
        $product->save();
        $admin->role = "ADMIN";
        $admin->save();
        $this->actingAs($admin, "web")->post("/api/admin/addProductImg", [
            "product_id" => $product->id,
            "image",
        ]);
    }
}
