<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Helpers\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;

class LogoutTest extends TestCase
{
    use RefreshDatabase, DatabaseMigrations;

    // logout test case for token expiry
    public function testUserIsLoggedOutProperly()
    {
        $user = factory(User::class)->create([
            'email' => 'user@test.com',
            'password' => bcrypt('12345678')
        ]);

        $token = auth()->attempt(['email' => 'user@test.com','password' => '12345678']);
        $headers = ['Authorization' => "Bearer $token"];

        $this->json('get', '/api/loans', [], $headers)->assertStatus(200);
        $this->json('post', '/api/logout', [], $headers)->assertStatus(200);
        $this->json('get', '/api/loans', [], $headers)->assertStatus(401);
    }
}
