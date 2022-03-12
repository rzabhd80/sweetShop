<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_add_product()
    {
        $user = User::create([
            "name" => "rand_name",
            "lastname" => "rand_lastname",
            "email" => "rand@gmail.com",
            "password" => Hash::make("something random"),
            "role" => "ADMIN"
        ]);
        $request = $this->post("/api/admin/new_product", [
            "product_name" => "fake_name",
            "available" => "true",
            "available_number" => "210"
        ]);
        $request->assertStatus(201);
        Product::create([$request]);
        $this->assertDatabaseCount(Product::find(1), 1);
    }
}
