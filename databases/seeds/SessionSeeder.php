<?php
use Illuminate\Database\Capsule\Manager as Capsule;

$sessions = [
    ['id' => 'session1', 'data' => serialize(['user_id'=>1]), 'created_at'=>date('Y-m-d H:i:s'), 'updated_at'=>date('Y-m-d H:i:s')],
    ['id' => 'session2', 'data' => serialize(['user_id'=>2]), 'created_at'=>date('Y-m-d H:i:s'), 'updated_at'=>date('Y-m-d H:i:s')],
];

Capsule::table('sessions')->insert($sessions);