<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
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
        $request->assertJsonStructure(["message","token"]);
    }
}
