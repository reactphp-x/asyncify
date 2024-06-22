<?php

require __DIR__ . '/../vendor/autoload.php';

use Reactphp\Framework\Asyncify\Asyncify;
use React\EventLoop\Loop;

$stream = Asyncify::call(function ($stream){

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
}, true);

$stream->on('data', function ($data) {
    var_dump($data);
});

$stream->on('close', function () {

});

$stream->on('error', function ($e) {
    var_dump($e->getMessage(), 'error');
});

Loop::addTimer(6, function () {
    Asyncify::terminate();
});