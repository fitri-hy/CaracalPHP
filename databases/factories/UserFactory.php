<?php
use Faker\Factory;
use Illuminate\Database\Capsule\Manager as Capsule;

$faker = Factory::create();

for ($i=0; $i<10; $i++) {
    Capsule::table('users')->insert([
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'created_at' => now(),
        'updated_at' => now()
    ]);
}