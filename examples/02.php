<?php

require __DIR__ . '/../vendor/autoload.php';

use Reactphp\Framework\Asyncify\Async;
use React\EventLoop\Loop;

$word = 'world';
$stream = Async::call(function () use ($word) {
    return [
        'hello' => $word
    ];
}, true);

$stream->on('data', function ($data) {
    var_dump($data);
});

$stream->on('close', function () {

});

$stream->on('error', function ($e) {
    var_dump($e->getMessage(), 'error');
});


Loop::addTimer(1, function () {
    Async::terminate();
});