<?php

namespace Tests\Feature;

use App\Http\Controllers\AdminController;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * creates fake admin
     *
     * @return User
     *
     * @author Reza
     *
     */

    private function createAdmin()
    {
        $user =  User::create([
            "name" => $this->faker()->name(),
            "lastname" => $this->faker()->lastName(),
            "email" => $this->faker()->email(),
            "password" => $this->faker()->password(),
        ]);
        $user->role = "ADMIN";
        $user->save();
        return $user;
    }
    /**
     * creates fake product
     *
     * @return Product
     *
     * @author Reza
     */

    private function createProduct()
    {
        return Product::create([
            "name" => $this->faker()->name,
            "available" => 1,
            "available_number" => $this->faker()->numberBetween(0, 20),
            "price" => random_int(10, 4000)
        ]);
    }


    public function test_add_product()
    {
        $this->withoutExceptionHandling();
        $user = $this->createAdmin();
        $request = $this->actingAs($user, "web")->post("/api/admin/new_product", [
            "product_name" => $this->faker()->name,
            "available" => 1,
            "available_number" => $this->faker()->numberBetween(0, 20),
            "price" => random_int(10, 4000)
        ]);
        $request->assertStatus(201);
        $this->assertDatabaseCount(Product::find(1), 1);
    }

    public function test_edit_product()
    {
        $user = $this->createAdmin();
        $product = $this->createProduct();
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
        $user = $this->createAdmin();
        $product = $this->createProduct();
        $product->save();
        $request = $this->actingAs($user, "web")->delete("api/admin/delete_product", ["product_id" => $product->id]);
        $request->assertOk();
        $this->assertDatabaseCount($product, 0);
    }

    public function test_add_user()
    {
        $admin = $this->createAdmin();
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
    public function test_admin_add_imageToProduct()
    {
        $admin = $this->createAdmin();
        $product = $this->createProduct();
        $product->save();
        $admin->role = "ADMIN";
        $admin->save();
        $this->actingAs($admin, "web")->post("/api/admin/addProductImg", [
            "product_id" => $product->id,
            "image",
        ]);
    }
}
