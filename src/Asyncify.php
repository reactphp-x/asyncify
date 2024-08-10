<?php

namespace ReactphpX\Asyncify;

use ReactphpX\ProcessManager\ProcessManager;
use React\Promise\Deferred;

class Asyncify
{
    static $number = 1;
    static $key = 'asyncify';

    public static function call(callable $callable, $isStream = false)
    {
        ProcessManager::instance(static::$key)->setNumber(static::$number);
        $stream = ProcessManager::instance(static::$key)->call($callable);

        if ($isStream) {
            return $stream;
        }

        $deferred = new Deferred();

        $data = null;
        $stream->on('data', function ($buffer) use (&$data) {
            $data .= $buffer;
        });

        $stream->on('close', function () use ($deferred, &$data) {
            $deferred->resolve($data);
            $data = null;
        });
        
        $stream->on('error', function ($e) use ($deferred) {
            $deferred->reject($e);
        });

        return $deferred->promise();
    }

    public static function __callStatic($method, $arguments)
    {
        ProcessManager::instance(static::$key)->{$method}(...$arguments);
    }
}
