<?php
use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->create('sessions', function ($table) {
    $table->string('id', 128)->primary();
    $table->longText('data');
    $table->timestamp('created_at')->useCurrent();
    $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
    $table->index('updated_at');
});