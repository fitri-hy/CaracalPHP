<?php
use Illuminate\Database\Capsule\Manager as Capsule;
use Ramsey\Uuid\Uuid;

$jobs = [
    [
        'id' => Uuid::uuid4()->toString(),
        'payload' => serialize(['job'=>'SendWelcomeEmail','user_id'=>1]),
        'processed' => false,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ],
    [
        'id' => Uuid::uuid4()->toString(),
        'payload' => serialize(['job'=>'SendNotification','user_id'=>2]),
        'processed' => false,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ],
];

Capsule::table('jobs')->insert($jobs);