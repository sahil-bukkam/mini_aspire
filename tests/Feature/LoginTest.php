<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Helpers\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;

class LoginTest extends TestCase
{
    use RefreshDatabase, DatabaseMigrations;
    
    // validation test case for login
    public function testRequiresEmailAndLogin()
    {
        $this->json('POST', 'api/login')
            ->assertStatus(422)
            ->assertJson([
                'errors' => ['The email field is required.','The password field is required.']
            ]);
    }

    // test case for login success
    public function testUserLoginsSuccessfully()
    {
        $user = factory(User::class)->create([
            'email' => 'bukkamsahil@gmail.com',
            'password' => bcrypt('12345678')
        ]);

        $payload = ['email' => 'bukkamsahil@gmail.com', 'password' => '12345678'];

        $this->json('POST', 'api/login', $payload)
            ->assertStatus(200)
            ->assertJsonStructure([
                "access_token",
                "token_type",
                "expires_in",
                "type"
            ]);

    }
}
