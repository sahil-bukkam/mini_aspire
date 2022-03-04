<?php

use Faker\Generator as Faker;
use App\Loan;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Installment::class, function (Faker $faker) {
    return [
        'loan_id' => function () {
            return factory(Loan::class)->create()->id;
        },
        'amount_paid' => $faker->randomDigitNotNull
    ];
});
