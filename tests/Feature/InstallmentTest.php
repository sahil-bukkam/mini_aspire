<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Helpers\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;
use App\Loan;
use App\Installment;

class InstallmentTest extends TestCase
{
    use RefreshDatabase, DatabaseMigrations;

    // create installment test case
    public function testsInstallmentsAreCreatedCorrectly()
    {
        $user = factory(User::class)->create([
            'email' => 'user@test.com',
            'password' => bcrypt('12345678'),
            'type' => 2
        ]);

        $token = auth()->attempt(['email' => 'user@test.com','password' => '12345678']);
        $headers = ['Authorization' => "Bearer $token"];

        $loan = factory(Loan::class)->create([
            'user_id' => $user->id,
            'amount' => 1000,
            'loan_term' => 20,
            'weekly_amount' => 50,
            'status' => 1
        ]);

        $payload = [
            "amount_paid" => 50
        ];

        $this->json('POST', 'api/loans/'.$loan->id.'/installments', $payload, $headers)
            ->assertStatus(201)
            ->assertJson(["data" => [
                "id" => 1,
                "loan_id" => 1,
                "amount_paid" => 50,
                "loan" => [
                    "id" => 1,
                    "user_id" => 1,
                    "status" => 1
                ]
            ]])
            ->assertJsonStructure([
                "data" => [
                "id",
                "loan_id",
                "amount_paid",
                "created_at",
                "updated_at",
                "loan" => [
                    "id",
                    "user_id",
                    "amount",
                    "loan_term",
                    "status",
                    "weekly_amount",
                    "amount_remaining",
                    "previous_weekly_amount",
                    "created_at",
                    "updated_at"
                ]
            ]
            ]);    
    }

    // validation test cases
    public function testsLoanValidationAttributes()
    {
        $user = factory(User::class)->create([
            'email' => 'user@test.com',
            'password' => bcrypt('12345678'),
            'type' => 2
        ]);

        $loan = factory(Loan::class)->create([
            'user_id' => $user->id,
            'amount' => 1000,
            'loan_term' => 20,
            'weekly_amount' => 50,
            'status' => 0,
            'amount_remaining' => 0
        ]);

        $token = auth()->attempt(['email' => 'user@test.com','password' => '12345678']);
        $headers = ['Authorization' => "Bearer $token"];

        // validation test case : The amount paid field is required
        $payload = [];
        $this->json('POST', 'api/loans/'.$loan->id.'/installments', $payload, $headers)
            ->assertStatus(422)
            ->assertJson([
                "errors" => [
                    "The amount paid field is required."
                ]
            ]);

        // validation test case : The amount paid must be a number.
        $payload = [
            "amount_paid" => 'hhhjk'
        ];
        $this->json('POST', 'api/loans/'.$loan->id.'/installments', $payload, $headers)
            ->assertStatus(422)
            ->assertJson([
                "errors" => [
                    "The amount paid must be a number."
                ]
            ]);

        // validation test case : You have already paid your all installments.
        $payload = [
            "amount_paid" => 50
        ];
        $this->json('POST', 'api/loans/'.$loan->id.'/installments', $payload, $headers)
            ->assertStatus(400)
            ->assertJson([
                'error' => 'You have already paid your all installments.'
            ]);

        $loan = factory(Loan::class)->create([
            'user_id' => $user->id,
            'amount' => 1000,
            'loan_term' => 20,
            'weekly_amount' => 50,
            'status' => 0,
            'amount_remaining' => 1000
        ]);

        // validation test case : Amount given does not match with the weekly installment amount
        $payload = [
            "amount_paid" => 40
        ];
        $this->json('POST', 'api/loans/'.$loan->id.'/installments', $payload, $headers)
            ->assertStatus(400)
            ->assertJson([
                'error' => 'Amount given does not match with the weekly installment amount'
            ]);

        // validation test case : Your loan is not approved yet.
        $payload = [
            "amount_paid" => 50
        ];
        $this->json('POST', 'api/loans/'.$loan->id.'/installments', $payload, $headers)
            ->assertStatus(400)
            ->assertJson([
                'error' => 'Your loan is not approved yet.'
            ]);
         
            
        $user = factory(User::class)->create([
            'email' => 'newuser@test.com',
            'password' => bcrypt('12345678'),
            'type' => 2
        ]);
        
        $token = auth()->attempt(['email' => 'newuser@test.com','password' => '12345678']);
        $headers = ['Authorization' => "Bearer $token"];

        // validation test case : Wrong user trying to pay installment
        $payload = [
            "amount_paid" => 50
        ];
        $this->json('POST', 'api/loans/'.$loan->id.'/installments', $payload, $headers)
            ->assertStatus(403)
            ->assertJson([
                'error' => 'Wrong user'
            ]);
    }

}
