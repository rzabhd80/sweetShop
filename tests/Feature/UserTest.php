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
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_register_user()
    {
        $this->withoutExceptionHandling();
        $response = $this->postJson("api/auth/register", [

            "name" => "what",
            "lastname" => "the",
            "email" => "fuck@user.com",
            "password" => "password",
        ]);
        $response->assertOk();
        $this->assertDatabaseCount(User::find(1), 1);
    }
    public function test_name_exists()
    {
        $response  = $this->postJson("/api/auth/register", [
            "lastname" => "something",
            "email" => "something@something.com",
            "password" => "some random password"
        ]);
        $response->assertJsonValidationErrorFor("name");
    }
    public function test_lastname_exists()
    {
        $response = $this->postJson("/api/auth/register", [
            "name" => "something",
            "email" => "something@something.com",
            "password" => "something random as password"
        ]);
        $response->assertJsonValidationErrors("lastname");
    }
    public function test_email_exists()
    {
        $response = $this->postJson("/api/auth/register", [
            "name" => "something",
            "lastname" => "something rand",
            "password" => "something random as password"
        ]);
        $response->assertJsonValidationErrors("email");
    }
    public function test_proper_password_exists()
    {
        $response = $this->postJson("/api/auth/register", [
            "name" => "something",
            "lastname" => "something rand",
            "lastname" => "email@email.com",
            "password" => "wtf"
        ]);
        $response->assertJsonValidationErrors("password");
    }
    public function test_user_login()
    {
        $user = User::create([
            "name" => "temp_name",
            "lastname" => "temp_lastname",
            "email" => "temp@gmail.com",
            "password" => Hash::make("temp_password")
        ]);
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
        $user = User::create([
            "name" => "temp_name",
            "lastname" => "temp_lastname",
            "email" => "temp@gmail.com",
            "password" => Hash::make("temp_password")
        ]);
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
    public function test_buy_available_product()
    {
        $user = User::create([
            "name" => "fake_name",
            "lastname" => "fake_lastname",
            "email" => "sthRand@gmail.com",
            "password" => Hash::make("sthRandAsPass")
        ]);
        $product = Product::create([
            "name" => "produnt_name",
            "available" => 1,
            "available_number" => 11,
            "price" => "1000"
        ]);
        $req = $this->actingAs($user, "web")->post("/api/user/buy_product", [
            "product_id"
        ]);
        $req->assertOk();
        $this->assertTrue(10, $product->available_count);
    }

    public function test_buy_unavailable_count()
    {
        $user = User::create([
            "name" => "fake_name",
            "lastname" => "fake_lastname",
            "email" => "sthRand@gmail.com",
            "password" => Hash::make("sthRandAsPass")
        ]);
        $req = $this->actingAs($user, "web")->post([
            "product_id" => "1",
        ]);
        $req->assertStatus(404);
    }
}
