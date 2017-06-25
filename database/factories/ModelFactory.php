<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'lname' => $faker->name,
        'fname' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
        'identity' => $faker->randomDigit,
        'address' => $faker->address,
        'postcode' => $faker->postcode,
        'phone' => $faker->phoneNumber,
        'state' => $faker->state,
        'city' => $faker->city,
        'role' => $faker->name,
        'nationality' => $faker->country,
        'gender' => 'male',
        'license_plate' => $faker->name,
        'online_status' => 'online',
        'status' => 'online',
    ];
});


