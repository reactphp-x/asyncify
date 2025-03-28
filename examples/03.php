<?php

require __DIR__ . '/../vendor/autoload.php';

use ReactphpX\Asyncify\Asyncify;
use React\EventLoop\Loop;

Asyncify::call(function ($stream){

    $i = 0;
    $timer = Loop::addPeriodicTimer(1, function () use ($stream, &$i) {
        $i++;
        $stream->write('hello world:'. $i);
    });
    Loop::addTimer(5, function () use ($timer, $stream) {
        Loop::cancelTimer($timer);
        $stream->end();
    });
    return $stream;
}, true)->then(function($stream){
    $stream->on('data', function ($data) {
        var_dump($data);
    });
    
    $stream->on('close', function () {
        var_dump('Stream closed');
    });
    
    $stream->on('error', function ($e) {
        var_dump($e->getMessage(), 'error');
    });
    
});

