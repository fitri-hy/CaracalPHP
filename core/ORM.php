<?php
namespace Caracal\Core;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

abstract class ORM extends Model
{
    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        $db = Application::getInstance()->db();
        if (!$db || !$db->isConnected()) {
            throw new RuntimeException("ORM is used but the database is not active.");
        }

        if (!class_exists(Capsule::class)) {
            throw new RuntimeException("Illuminate Database Capsule is not yet available.");
        }

        parent::__construct($attributes);
    }

    public static function table(): \Illuminate\Database\Query\Builder
    {
        return static::query();
    }
}