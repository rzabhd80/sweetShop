<?php

namespace Tests\Feature;


use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * creates fake user for testing
     *
     * @return User
     *
     * @author Reza
     */

    private function createUser()
    {
        $user = User::create([
            "name" => $this->faker()->name(),
            "lastname" => $this->faker()->lastName(),
            "email" => $this->faker()->email(),
            "password" => Hash::make("temp_password")
        ]);
        $user->role = "USER";
        $user->save();
        return $user;
    }

    public function test_register_user()
    {
        $req = [
            "name" => $this->faker()->name(),
            "lastname" => $this->faker()->lastName(),
            "email" => $this->faker()->email(),
            "password" => "temp_password",
        ];
        $this->withoutExceptionHandling();
        $response = $this->postJson("api/auth/register", $req);
        $response->assertOk();
        $user = User::where("email", $req["email"])->first();

        $this->assertDatabaseHas("users", $user->getAttributes());
    }
    public function test_name_exists()
    {
        $response  = $this->postJson("/api/auth/register", [
            "lastname" => $this->faker()->lastName(),
            "email" => $this->faker()->email(),
            "password" => $this->faker()->password()
        ]);
        $response->assertJsonValidationErrorFor("name");
    }
    public function test_lastname_exists()
    {
        $response = $this->postJson("/api/auth/register", [
            "name" => $this->faker()->name(),
            "email" => $this->faker()->email(),
            "password" => $this->faker()->password(),
        ]);
        $response->assertJsonValidationErrors("lastname");
    }
    public function test_email_exists()
    {
        $response = $this->postJson("/api/auth/register", [
            "name" => $this->faker()->name(),
            "lastname" => $this->faker()->lastName(),
            "password" => $this->faker()->password(),
        ]);
        $response->assertJsonValidationErrors("email");
    }
    public function test_proper_password_exists()
    {
        $response = $this->postJson("/api/auth/register", [
            "name" => $this->faker()->name(),
            "lastname" => $this->faker()->lastName(),
            "email" => $this->faker()->email(),
            "password" => "wtf"
        ]);
        $response->assertJsonValidationErrors("password");
    }
    public function test_user_login()
    {
        $user = $this->createUser();
        $request = $this->post("/api/auth/login", [
            "email" => $user->email,
            "password" => "temp_password"
        ]);
        $request->assertOk();
        $request->assertJsonStructure(["message", "token"]);
    }
    public function test_user_logout()
    {
        $this->withoutExceptionHandling();
        $user = $this->createUser();
        $login_user = $this->post("/api/auth/login", [
            "email" => $user->email,
            "password" => "temp_password"
        ]);
        $login_user->assertOk();
        Auth::login($user);
        $request = $this->post("/api/auth/logout");
        $request->assertOk();
        $request->assertJsonStructure(["message"]);
    }

    public function test_user_update_password()
    {
        $user = User::create([
            "name" => "fake_name",
            "lastname" => "fake_lastname",
            "email" => "fake@gmail.com",
            "password" => Hash::make("fake_password")
        ]);
        auth()->login($user);
        $response = $this->actingAs($user, "web")->put("api/users/edit_password", [
            "password_old" => "old_password",
            "new_password" => "new_password"
        ]);
        $response->assertOk();
        $this->assertTrue(Hash::check("fake_password", auth()->user()->password));
    }
    public function test_update_user_email()
    {
        $user = User::create([
            "name" => "fake_name",
            "lastname" => "fake_lastname",
            "email" => "fake1@email.com",
            "password" => Hash::make("fake_password")
        ]);
        Auth::login($user);
        $request = $this->actingAs($user, "web")->put("api/users/edit_email", [
            "new_email" => "fake2@gmail.com"
        ]);
        $request->assertOk();
        $content = $request->decodeResponseJson();
        $this->assertEquals($content["new email"], "fake2@gmail.com");
    }

    public function test_but_available_product()
    {
        $user = User::create([
            "name" => "fake_name",
            "lastname" => "fake_lastname",
            "email" => "sthRand@gmail.com",
            "password" => Hash::make("sthRandAsPass")
        ]);
        $product = Product::create([
            "name" => "product_name",
            "available" => 1,
            "available_number" => 11,
            "price" => "1000"
        ]);
        $product->save();
        $req = $this->actingAs($user, "web")->post("/api/users/buy_product", [
            "product_id" => $product->id,
        ]);
        $req->assertOk();
        $res_product = Product::find($product->id);
        $this->assertEquals(10, $res_product->available_number);
    }
}
