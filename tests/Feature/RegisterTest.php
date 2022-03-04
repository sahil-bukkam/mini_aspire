<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Helpers\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;

class RegisterTest extends TestCase
{
    use RefreshDatabase, DatabaseMigrations;
    
    // registration success test case
    public function testsRegistersSuccessfully()
    {
        $payload = [
            'name' => 'John',
            'email' => 'john@gmail.com',
            'password' => 'john1234',
            'password_confirmation' => 'john1234',
            'contact_number' => 9653456321,
            'type' => 2
        ];

        $this->json('POST', 'api/register', $payload)
            ->assertStatus(200)
            ->assertJsonStructure([
                "access_token",
                "token_type",
                "expires_in",
                "type"
            ]);
    }

    // validation test case
    public function testsRequiresAttributes()
    {
        $this->json('POST', 'api/register')
            ->assertStatus(422)
            ->assertJson([
                "errors" => [
                    "The name field is required.",
                    "The email field is required.",
                    "The password field is required.",
                    "The contact number field is required.",
                    "The type field is required."
                ]
            ]);
    }

    // test case for confirm password
    public function testsRequirePasswordConfirmation()
    {
        $payload = [
            'name' => 'John',
            'email' => 'john@gmail.com',
            'password' => 'john1234',
        ];

        $this->json('POST', 'api/register', $payload)
            ->assertStatus(422)
            ->assertJson([
                'errors' => ['The password confirmation does not match.']
            ]);
    }
}
