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

$factory->define(App\User::class, function ($faker) {
    return [
        'name' => $faker->name,
        'email' => 'johndoe@example.com',
        'password' => app('hash')->make('12345'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Enviroment::class, function (Faker\Generator $faker){
    return [
        'type' => 'school',
        'name' => 'IFC - Araquari'
    ];
});

$factory->define(App\Person::class, function (Faker\Generator $faker){
    return [
        'name' => $faker->name,
        'photo' => 'qrcode',
    ];
});

$factory->define(App\Student::class, function (Faker\Generator $faker) {
    return [
        'registry'  => rand(999999,1999999),
        'isBoarder' => array_rand([0,1]),
    ];
});

$factory->define(App\LunchPattern::class, function (Faker\Generator $faker) use (&$iPattern) {
    return [
        'sunday'    => array_rand([0,1]),
        'monday'    => array_rand([0,1]),
        'tuesday'   => array_rand([0,1]),
        'wednesday' => array_rand([0,1]),
        'thursday'  => array_rand([0,1]),
        'friday'    => array_rand([0,1]),
        'saturday'  => array_rand([0,1]),
    ];
});



$factory->define(App\RecordFather::class, function (Faker\Generator $faker) {
    return [];
});

$factory->define(App\RecordChildren::class, function (Faker\Generator $faker) {
    return [];
});

$factory->define(App\PaymentType::class, function (Faker\Generator $faker) {
    return [
        'value' => 7.75,
        'type' => 'byEnviroment'
    ];
});