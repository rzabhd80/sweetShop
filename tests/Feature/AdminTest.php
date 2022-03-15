<?php

namespace Tests\Feature;

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
        Product::create([$request]);
        $this->assertDatabaseCount(Product::find(1), 1);
    }
}
