<?php

namespace ReactphpX\Asyncify;

use ReactphpX\ProcessManager\ProcessManager;
use React\Promise\Deferred;
use function React\Async\await;

class Asyncify
{
    static $processmanager;


    public static function call(callable $callable, $isStream = false, $prioritize = 0)
    {
        if (!static::$processmanager) {
            static::init();
        }

        $stream = await(static::$processmanager->run($callable, $prioritize));

        if ($isStream) {
            return $stream;
        }

        $deferred = new Deferred(function() use ($stream){
            $stream->close();
        });

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

    public static function init($min = 1, $max = 1)
    {
        if (static::$processmanager) {
            return;
        }
        static::$processmanager = new ProcessManager(sprintf(
            'exec php %s/child_process_init.php',
            __DIR__
        ), $min, $max);

    }
}
