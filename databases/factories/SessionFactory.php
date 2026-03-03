<?php
use Ramsey\Uuid\Uuid;
use Illuminate\Database\Capsule\Manager as Capsule;

for ($i=0;$i<5;$i++) {
    Capsule::table('sessions')->insert([
        'id' => Uuid::uuid4()->toString(),
        'data' => serialize(['user'=>'User'.$i]),
        'created_at'=>now(),
        'updated_at'=>now()
    ]);
}