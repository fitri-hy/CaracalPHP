<?php
use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->create('jobs', function ($table) {
    $table->uuid('id')->primary();
    $table->longText('payload');
    $table->boolean('processed')->default(false);
    $table->timestamp('created_at')->useCurrent();
    $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
    $table->index('processed');
});