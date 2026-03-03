<?php
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Support\Str;

$users = [
    ['name' => 'Alice', 'email' => 'alice@example.com', 'password' => password_hash('password123', PASSWORD_BCRYPT)],
    ['name' => 'Bob', 'email' => 'bob@example.com', 'password' => password_hash('secret456', PASSWORD_BCRYPT)],
];

foreach ($users as &$user) {
    $user['role'] = 'user';
    $user['created_at'] = date('Y-m-d H:i:s');
    $user['updated_at'] = date('Y-m-d H:i:s');
}

Capsule::table('users')->insert($users);