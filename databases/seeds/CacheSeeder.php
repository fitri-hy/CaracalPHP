<?php
use Illuminate\Database\Capsule\Manager as Capsule;

$caches = [
    ['key'=>'site_name', 'value'=>serialize('Caracal Demo'), 'created_at'=>date('Y-m-d H:i:s'), 'updated_at'=>date('Y-m-d H:i:s')],
    ['key'=>'maintenance_mode', 'value'=>serialize(false), 'created_at'=>date('Y-m-d H:i:s'), 'updated_at'=>date('Y-m-d H:i:s')],
];

Capsule::table('cache')->insert($caches);