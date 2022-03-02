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
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
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
}
