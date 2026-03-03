<?php
use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->create('cache', function ($table) {
    $table->string('key', 191)->primary();
    $table->longText('value');
    $table->timestamp('created_at')->useCurrent();
    $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
});