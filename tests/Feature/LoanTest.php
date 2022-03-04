<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Helpers\RefreshDatabase;
use App\User;
use App\Loan;

class LoanTest extends TestCase
{
    use RefreshDatabase;

    // create loan test case
    public function testsLoansAreCreatedCorrectly()
    {
        $user = factory(User::class)->create([
            'email' => 'user@test.com',
            'password' => bcrypt('12345678'),
            'type' => 2
        ]);

        $token = auth()->attempt(['email' => 'user@test.com','password' => '12345678']);
        $headers = ['Authorization' => "Bearer $token"];

        $payload = [
            "amount" => 1000,
            "loan_term" => 20
        ];

        $this->json('POST', 'api/loans', $payload, $headers)
            ->assertStatus(201)
            ->assertJson(["data" => [
                "id" => 1,
                "user_id" => 1,
                "amount" => 1000,
                "loan_term" => 20,
                "status" => 0,
                "weekly_amount" => 50,
                "amount_remaining" => 1000,
                "previous_weekly_amount" => 50,
                "user" => [
                    "id" => 1,
                    "email" => "user@test.com",
                    "type" => 2
                ],
                "installments" => []
            ]])
            ->assertJsonStructure([
                "data" => [
                    "id",
                    "user_id",
                    "amount",
                    "loan_term",
                    "status",
                    "weekly_amount",
                    "amount_remaining",
                    "previous_weekly_amount",
                    "created_at",
                    "updated_at",
                    "user" => [
                        "id",
                        "name",
                        "email",
                        "contact_number",
                        "type",
                        "created_at",
                        "updated_at"
                    ],
                    "installments" => []
                ]
            ]);    
    }

    // validation test case for creating loan
    public function testsLoanValidationAttributes()
    {
        $user = factory(User::class)->create([
            'email' => 'user@test.com',
            'password' => bcrypt('12345678'),
            'type' => 2
        ]);

        $token = auth()->attempt(['email' => 'user@test.com','password' => '12345678']);
        $headers = ['Authorization' => "Bearer $token"];
        $payload = [];
        $this->json('POST', 'api/loans', $payload, $headers)
            ->assertStatus(422)
            ->assertJson([
                "errors" => [
                    "The amount field is required.",
                    "The loan term field is required."
                ]
            ]);
        $payload = [
            "amount" => 'hhhjk',
            "loan_term" => 'hhujhj'
        ];
        $this->json('POST', 'api/loans', $payload, $headers)
            ->assertStatus(422)
            ->assertJson([
                "errors" => [
                    "The amount must be a number.",
                    "The loan term must be an integer."
                ]
            ]);
            
    }

    // test case for Only Customer can request a loan
    public function testsOnlyCustomerCanRequestLoan()
    {
        $user = factory(User::class)->create([
            'email' => 'user@test.com',
            'password' => bcrypt('12345678'),
            'type' => 1 // 1 means admin
        ]);

        $token = auth()->attempt(['email' => 'user@test.com','password' => '12345678']);
        $headers = ['Authorization' => "Bearer $token"];
        $payload = [
            "amount" => 1000,
            "loan_term" => 20
        ];
        $this->json('POST', 'api/loans', $payload, $headers)
            ->assertStatus(403)
            ->assertJson([
                'error' => 'Only Customer can request a loan.'
            ]);
    }

    // test case for approve/reject loan
    public function testsLoansAreUpdatedCorrectly()
    {
        $user = factory(User::class)->create([
            'email' => 'user@test.com',
            'password' => bcrypt('12345678'),
            'type' => 1
        ]);

        $token = auth()->attempt(['email' => 'user@test.com','password' => '12345678']);
        $headers = ['Authorization' => "Bearer $token"];

        $loan = factory(Loan::class)->create([
            'user_id' => $user->id
        ]);

        $payload = [
            "status" => 1
        ];

        $response = $this->json('PUT', 'api/loans/' . $loan->id, $payload, $headers)
            ->assertStatus(200)
            ->assertJson(["data" => [
                "id" => 1,
                "user_id" => 1,
                "status" => 1,
                "user" => [
                    "id" => 1,
                    "email" => "user@test.com",
                    "type" => 1
                ],
                "installments" => []
            ]])
            ->assertJsonStructure([
                "data" => [
                    "id",
                    "user_id",
                    "amount",
                    "loan_term",
                    "status",
                    "weekly_amount",
                    "amount_remaining",
                    "previous_weekly_amount",
                    "created_at",
                    "updated_at",
                    "user" => [
                        "id",
                        "name",
                        "email",
                        "contact_number",
                        "type",
                        "created_at",
                        "updated_at"
                    ],
                    "installments" => []
                ]
            ]);
    }

    // test case for Only Admin can change the status of all loans
    public function testsLoansAreUpdatedByAdminOnly()
    {
        $user = factory(User::class)->create([
            'email' => 'user@test.com',
            'password' => bcrypt('12345678'),
            'type' => 2
        ]);

        $token = auth()->attempt(['email' => 'user@test.com','password' => '12345678']);
        $headers = ['Authorization' => "Bearer $token"];

        $loan = factory(Loan::class)->create([
            'user_id' => $user->id
        ]);

        $payload = [
            "status" => 1
        ];

        $response = $this->json('PUT', 'api/loans/' . $loan->id, $payload, $headers)
            ->assertStatus(403)
            ->assertJson([
                'error' => 'Only Admin can change the status of all loans.'
            ]);
    }

    // test case update request validation
    public function testsLoansRequiredParamForUpdate()
    {
        $user = factory(User::class)->create([
            'email' => 'user@test.com',
            'password' => bcrypt('12345678'),
            'type' => 1
        ]);

        $token = auth()->attempt(['email' => 'user@test.com','password' => '12345678']);
        $headers = ['Authorization' => "Bearer $token"];

        $loan = factory(Loan::class)->create([
            'user_id' => $user->id
        ]);

        $payload = [];

        $response = $this->json('PUT', 'api/loans/' . $loan->id, $payload, $headers)
            ->assertStatus(422)
            ->assertJson([
                "errors" => [
                    "The status field is required."
                ]
            ]);


        $payload = ["status" => 3];

        $response = $this->json('PUT', 'api/loans/' . $loan->id, $payload, $headers)
            ->assertStatus(422)
            ->assertJson([
                "errors" => [
                    "The selected status is invalid."
                ]
            ]);
    }

    // test case for user cannot update status if installments are already started
    public function testsLoansInstallmentAlreadyStarted()
    {
        $user = factory(User::class)->create([
            'email' => 'user@test.com',
            'password' => bcrypt('12345678'),
            'type' => 1
        ]);

        $token = auth()->attempt(['email' => 'user@test.com','password' => '12345678']);
        $headers = ['Authorization' => "Bearer $token"];

        $loan = factory(Loan::class)->create([
            'user_id' => $user->id,
            'amount_remaining' => 500,
            'amount' => 1000
        ]);

        $payload = ["status" => 1];

        $response = $this->json('PUT', 'api/loans/' . $loan->id, $payload, $headers)
            ->assertStatus(400)
            ->assertJson([
                'error' => 'You cannot approve/reject the loan application as installments are already started.'
            ]);
    }

    // test case for loan listing
    public function testsLoansAreListedCorrectly()
    {
        $user = factory(User::class)->create([
            'email' => 'user@test.com',
            'password' => bcrypt('12345678'),
            'type' => 1
        ]);

        $token = auth()->attempt(['email' => 'user@test.com','password' => '12345678']);
        $headers = ['Authorization' => "Bearer $token"];

        $loan = factory(Loan::class)->create([
            'user_id' => $user->id
        ]);
        $loan = factory(Loan::class)->create([
            'user_id' => $user->id
        ]);

        $response = $this->json('GET', 'api/loans', [], $headers)
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    [
                        "id" => 1,
                        "user_id" => 1,
                        "user" => [
                            "id" => 1,
                            "email" => "user@test.com",
                            "type" => 1
                        ],
                        "installments"=> []
                    ],
                    [
                        "id" => 2,
                        "user_id" => 1,
                        "user" => [
                            "id" => 1,
                            "email" => "user@test.com",
                            "type" => 1
                        ],
                        "installments" => []
                    ]
                ]
            ])
            ->assertJsonStructure([
                "data" => [
                    "*" => [
                        "id",
                        "user_id",
                        "amount",
                        "loan_term",
                        "status",
                        "weekly_amount",
                        "amount_remaining",
                        "previous_weekly_amount",
                        "created_at",
                        "updated_at",
                        "user" => [
                            "id",
                            "name",
                            "email",
                            "contact_number",
                            "type",
                            "created_at",
                            "updated_at"
                        ],
                        "installments" => [
                        "*" =>  [
                                "id",
                                "loan_id",
                                "amount_paid",
                                "created_at",
                                "updated_at"
                            ]
                        ]
                    ]
                ]
            ]);

    }

    // test case to get a individual loan
    public function testsGetIndividualLoan()
    {
        $user = factory(User::class)->create([
            'email' => 'user@test.com',
            'password' => bcrypt('12345678'),
            'type' => 1
        ]);

        $token = auth()->attempt(['email' => 'user@test.com','password' => '12345678']);
        $headers = ['Authorization' => "Bearer $token"];

        $loan = factory(Loan::class)->create([
            'user_id' => $user->id
        ]);

        $response = $this->json('GET', 'api/loans/' . $loan->id, [], $headers)
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                        "id" => 1,
                        "user_id" => 1,
                        "user" => [
                            "id" => 1,
                            "email" => "user@test.com",
                            "type" => 1
                        ],
                        "installments"=> []
                ]
            ])
            ->assertJsonStructure([
                "data" => [
                        "id",
                        "user_id",
                        "amount",
                        "loan_term",
                        "status",
                        "weekly_amount",
                        "amount_remaining",
                        "previous_weekly_amount",
                        "created_at",
                        "updated_at",
                        "user" => [
                            "id",
                            "name",
                            "email",
                            "contact_number",
                            "type",
                            "created_at",
                            "updated_at"
                        ],
                        "installments" => [
                        "*" =>  [
                                "id",
                                "loan_id",
                                "amount_paid",
                                "created_at",
                                "updated_at"
                            ]
                        ]
                ]
            ]);
    }

}
