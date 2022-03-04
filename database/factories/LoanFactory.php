<?php

use Faker\Generator as Faker;
use App\User;

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

$factory->define(App\Loan::class, function (Faker $faker) {
    $amount = $faker->numberBetween($min = 1000, $max = 10000);
    $term = $faker->numberBetween($min = 5, $max = 50);
    $weeklyAmount = round($amount/$term,2);
    return [
        'user_id' => $faker->randomDigitNotNull,
        'amount' => $amount,
        'loan_term' => $term,
        'weekly_amount' => $weeklyAmount,
        'previous_weekly_amount' => $weeklyAmount,
        'status' => 0,
        'amount_remaining' => $amount
    ];
});
