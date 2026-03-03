<?php
namespace Caracal\Core;

class Event
{
    protected static array $listeners = [];
    protected static array $onceListeners = [];

    public static function on(string $event, callable $callback): void
    {
        self::$listeners[$event][] = $callback;
    }
	
    public static function once(string $event, callable $callback): void
    {
        self::$onceListeners[$event][] = $callback;
    }

    public static function trigger(string $event, array $data = []): array
    {
        $results = [];

        foreach (self::$listeners[$event] ?? [] as $callback) {
            $results[] = call_user_func($callback, $data);
        }

        foreach (self::$onceListeners[$event] ?? [] as $callback) {
            $results[] = call_user_func($callback, $data);
        }

        unset(self::$onceListeners[$event]);

        return $results;
    }

    public static function off(string $event, ?callable $callback = null): void
    {
        if ($callback === null) {
            unset(self::$listeners[$event], self::$onceListeners[$event]);
        } else {
            foreach ([&self::$listeners, &self::$onceListeners] as &$listArray) {
                if (isset($listArray[$event])) {
                    foreach ($listArray[$event] as $i => $cb) {
                        if ($cb === $callback) {
                            unset($listArray[$event][$i]);
                        }
                    }
                    $listArray[$event] = array_values($listArray[$event]);
                }
            }
        }
    }
}