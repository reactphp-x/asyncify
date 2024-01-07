<?php

namespace Reactphp\Framework\Asyncify;

use Reactphp\Framework\Process\ProcessManager;
use React\Stream\ReadableStreamInterface;
use React\Promise\PromiseInterface;
use React\Promise\Deferred;

class Async
{
    static $number = 1;
    static $key = 'asyncify';

    public static function call(callable $callable, $isStream = false): PromiseInterface | ReadableStreamInterface
    {
        ProcessManager::instance(static::$key)->initProcessNumber(static::$number);
        $deferred = new Deferred();
        $stream = ProcessManager::instance(static::$key)->callback(function ($stream) use ($callable) {
            $res = $callable();
            if ($res instanceof ReadableStreamInterface) {
                $res->on('data', [$stream, 'write']);
                $res->on('close', [$stream, 'end']);
                $res->on('end', [$stream, 'end']);
                return $stream;
            } else if ($res instanceof PromiseInterface) {
                $res->then(function ($data) use ($stream) {
                    $stream->end($data);
                }, function ($e) use ($stream) {
                    $stream->emit('error', [$e]);
                })->catch(function ($e) use ($stream) {
                    $stream->emit('error', [$e]);
                });
                return $stream;
            } else {
                return $res;
            }
        });

        if ($isStream) {
            return $stream;
        }

        $haveData = false;
        $stream->on('data', function ($buffer) use ($deferred, &$haveData) {
            $haveData = true;
            $deferred->resolve($buffer);
        });

        $stream->on('close', function () use ($deferred, &$haveData) {
            if (!$haveData) {
                $deferred->resolve(null);
            }
        });
        
        $stream->on('error', function ($e) use (&$data, &$isError, $deferred) {
            $isError = true;
            $data = null;
            $deferred->reject($e);
        });

        return $deferred->promise();
    }

    public static function __callStatic($method, $arguments)
    {
        ProcessManager::instance(static::$key)->{$method}(...$arguments);
    }
}
